<?php

namespace App\Livewire\Incidents;

use App\Domain\Incident\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public int $incidentId;

    // Agency processing fields
    #[Validate('required|string')]
    public string $repair_details = '';

    #[Validate('required|numeric|min:0')]
    public float $repair_cost = 0;

    // Tenant confirmation fields
    #[Validate('nullable|string')]
    public string $tenant_confirmation_note = '';

    #[Validate('required|image|max:10240')]
    public $confirmation_photo = null;

    public function mount(int $incidentId): void
    {
        $this->incidentId = $incidentId;
        $incident = Incident::findOrFail($incidentId);

        $this->authorize('view', $incident);

        $this->repair_details = $incident->repair_details ?? '';
        $this->repair_cost = (float) $incident->repair_cost;
        $this->tenant_confirmation_note = $incident->tenant_confirmation_note ?? '';
    }

    public function takeInCharge(): void
    {
        $incident = Incident::findOrFail($this->incidentId);
        $this->authorize('update', $incident);

        $incident->update([
            'status' => 'in_progress',
        ]);

        session()->flash('success', "L'incident {$incident->reference} est maintenant en cours de traitement.");
    }

    public function resolve(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isTenant()) {
            abort(403, 'Seule l\'agence peut indiquer la résolution des travaux.');
        }

        $incident = Incident::findOrFail($this->incidentId);
        $this->authorize('update', $incident);

        $this->validate([
            'repair_details' => 'required|string',
            'repair_cost'    => 'required|numeric|min:0',
        ]);

        $incident->update([
            'status'         => 'resolved',
            'repair_details' => $this->repair_details,
            'repair_cost'    => $this->repair_cost,
            'resolved_at'    => now(),
        ]);

        session()->flash('success', "L'incident a été marqué comme traité par l'agence. En attente de confirmation du locataire.");
    }

    public function confirmResolution(): void
    {
        $incident = Incident::findOrFail($this->incidentId);
        $this->authorize('update', $incident);

        $this->validate([
            'confirmation_photo'       => 'required|image|max:10240',
            'tenant_confirmation_note' => 'nullable|string',
        ]);

        $photoPath = is_object($this->confirmation_photo) ? $this->confirmation_photo->store('incidents/confirmations', 'public') : (string)$this->confirmation_photo;

        $incident->update([
            'status'                    => 'closed',
            'tenant_confirmation_photo' => $photoPath,
            'tenant_confirmation_note'  => $this->tenant_confirmation_note,
            'closed_at'                 => now(),
        ]);

        session()->flash('success', "Merci ! La réparation a été confirmée et l'incident est désormais clôturé.");
    }

    public function render(): \Illuminate\View\View
    {
        $incident = Incident::with(['property', 'tenant', 'lease', 'agency'])
            ->findOrFail($this->incidentId);

        return view('livewire.incidents.show', compact('incident'));
    }
}
