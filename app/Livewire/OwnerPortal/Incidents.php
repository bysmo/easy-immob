<?php

namespace App\Livewire\OwnerPortal;

use App\Domain\Incident\Enums\IncidentStatus;
use App\Domain\Incident\Models\Incident;
use App\Domain\Property\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Incidents & Réparations côté bailleur.
 * Le bailleur peut approuver le coût de réparation pour les incidents "resolved".
 */
class Incidents extends Component
{
    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $propertyFilter = '';

    // Modale Fiche / Confirmation bailleur
    public bool $showConfirmModal = false;
    public ?int $confirmIncidentId = null;
    public ?Incident $selectedIncident = null;
    public float $confirmedAmount = 0.0;

    #[Validate('nullable|string|max:1000')]
    public ?string $confirmNote = null;

    public function viewIncident(int $incidentId): void
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        $incident = Incident::withoutGlobalScopes()
            ->with(['property', 'agency', 'tenant', 'lease'])
            ->findOrFail($incidentId);

        // Vérifier que c'est bien un bien de ce bailleur
        $property = Property::withoutGlobalScopes()->find($incident->property_id);
        if (! $property || $property->owner_id !== $owner?->id) {
            abort(403);
        }

        $this->selectedIncident  = $incident;
        $this->confirmIncidentId = $incidentId;
        $this->confirmedAmount   = (float) ($incident->owner_confirmed_amount ?: ($incident->repair_cost ?: 0.0));
        $this->confirmNote       = $incident->owner_confirmation_note;
        $this->showConfirmModal  = true;
    }

    public function confirmRepair(int $incidentId): void
    {
        $this->viewIncident($incidentId);
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->selectedIncident = null;
    }

    public function saveConfirmRepair(): void
    {
        $this->validate([
            'confirmedAmount' => ['required', 'numeric', 'min:0'],
            'confirmNote'     => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        $incident = Incident::withoutGlobalScopes()->findOrFail($this->confirmIncidentId);

        // Re-vérification
        $property = Property::withoutGlobalScopes()->find($incident->property_id);
        if (! $property || $property->owner_id !== $owner?->id) {
            abort(403);
        }

        $incident->update([
            'owner_confirmed_at'     => now(),
            'owner_confirmed_amount' => $this->confirmedAmount,
            'owner_confirmation_note'=> $this->confirmNote,
            'status'                 => IncidentStatus::Closed,
            'closed_at'              => now(),
        ]);

        $this->showConfirmModal = false;
        $this->selectedIncident = null;
        session()->flash('success', 'Réparation approuvée et confirmée avec succès.');
    }

    public function rejectRepair(): void
    {
        $this->validate([
            'confirmNote' => ['required', 'string', 'max:1000'],
        ], [
            'confirmNote.required' => 'Veuillez saisir un motif pour justifier le rejet de la réparation.',
        ]);

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        $incident = Incident::withoutGlobalScopes()->findOrFail($this->confirmIncidentId);

        // Re-vérification
        $property = Property::withoutGlobalScopes()->find($incident->property_id);
        if (! $property || $property->owner_id !== $owner?->id) {
            abort(403);
        }

        $incident->update([
            'owner_confirmed_at'     => now(),
            'owner_confirmed_amount' => 0,
            'owner_confirmation_note'=> $this->confirmNote,
            'status'                 => IncidentStatus::Rejected,
        ]);

        $this->showConfirmModal = false;
        $this->selectedIncident = null;
        session()->flash('success', 'La réparation a été rejetée.');
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        if (! $owner) {
            abort(403);
        }

        $propertyIds = Property::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->pluck('id');

        $incidents = Incident::withoutGlobalScopes()
            ->whereIn('property_id', $propertyIds)
            ->with(['property'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->propertyFilter, fn ($q) => $q->where('property_id', $this->propertyFilter))
            ->latest()
            ->get();

        $properties = Property::withoutGlobalScopes()
            ->whereIn('id', $propertyIds)
            ->orderBy('title')
            ->get();

        return view('livewire.owner-portal.incidents', [
            'owner'      => $owner,
            'incidents'  => $incidents,
            'properties' => $properties,
            'statuses'   => IncidentStatus::cases(),
        ]);
    }
}
