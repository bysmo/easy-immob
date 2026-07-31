<?php

namespace App\Livewire\Agency;

use App\Domain\Agency\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public string $name = '';
    public ?string $legal_name = null;
    public string $email = '';
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $nif_rccm = null;

    public float $commission_rate = 10.0;
    public bool $is_subject_to_tva = true;
    public float $tva_rate = 18.0;

    /** @var mixed */
    public $logo = null;
    public ?string $existingLogoUrl = null;

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            abort(403, 'Accès réservé aux utilisateurs rattachés à une agence.');
        }

        $agency = Agency::findOrFail($user->agency_id);

        $this->name              = $agency->name ?? '';
        $this->legal_name         = $agency->legal_name;
        $this->email             = $agency->email ?? '';
        $this->phone             = $agency->phone;
        $this->address           = $agency->address;
        $this->nif_rccm          = $agency->nif_rccm;
        $this->commission_rate   = (float) ($agency->commission_rate ?? 10.0);
        $this->is_subject_to_tva = (bool) ($agency->is_subject_to_tva ?? true);
        $this->tva_rate          = (float) ($agency->tva_rate ?? 18.0);
        $this->existingLogoUrl   = $agency->logo_url;
    }

    public function updatedIsSubjectToTva($value): void
    {
        if (!$value) {
            $this->tva_rate = 0.0;
        } elseif ($this->tva_rate == 0) {
            $this->tva_rate = 18.0;
        }
    }

    public function save(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $this->validate([
            'name'              => 'required|string|max:255',
            'legal_name'         => 'nullable|string|max:255',
            'email'              => 'required|email|max:255',
            'phone'              => 'nullable|string|max:50',
            'address'            => 'nullable|string|max:255',
            'nif_rccm'           => 'nullable|string|max:100',
            'commission_rate'    => 'required|numeric|min:0|max:100',
            'is_subject_to_tva'  => 'boolean',
            'tva_rate'           => 'nullable|numeric|min:0|max:100',
            'logo'               => 'nullable|image|max:2048',
        ]);

        $agency = Agency::findOrFail($user->agency_id);

        if ($this->logo) {
            if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
                Storage::disk('public')->delete($agency->logo_path);
            }

            $path = $this->logo->store('agencies/logos', 'public');
            $agency->logo_path = $path;
        }

        $agency->name              = $this->name;
        $agency->legal_name         = $this->legal_name;
        $agency->email             = $this->email;
        $agency->phone             = $this->phone;
        $agency->address           = $this->address;
        $agency->nif_rccm          = $this->nif_rccm;
        $agency->commission_rate   = $this->commission_rate;
        $agency->is_subject_to_tva = $this->is_subject_to_tva;
        $agency->tva_rate          = $this->is_subject_to_tva ? ($this->tva_rate ?? 18.0) : 0.0;

        $agency->save();

        $this->logo = null;
        $this->existingLogoUrl = $agency->fresh()->logo_url;

        session()->flash('message', 'Les informations et paramètres de votre agence ont été enregistrés avec succès !');
    }

    public function removeLogo(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $agency = Agency::findOrFail($user->agency_id);

        if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
            Storage::disk('public')->delete($agency->logo_path);
        }

        $agency->logo_path = null;
        $agency->save();

        $this->existingLogoUrl = null;
        $this->logo = null;

        session()->flash('message', "Le logo de l'agence a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.agency.settings');
    }
}
