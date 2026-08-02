<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    // Informations personnelles
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public $avatar = null;

    // Mot de passe
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Paramètres de l'agence
    public ?float $agency_commission_rate = 10.0;
    public bool $agency_is_subject_to_tva = true;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name  = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';

        if ($user->agency && ! $user->isOwner()) {
            $this->agency_commission_rate = (float) ($user->agency->commission_rate ?? 10.0);
            $this->agency_is_subject_to_tva = (bool) ($user->agency->is_subject_to_tva ?? true);
        }
    }

    public function updateAgencySettings(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->agency || $user->isOwner()) {
            return;
        }

        $validated = $this->validate([
            'agency_commission_rate'   => 'required|numeric|min:0|max:100',
            'agency_is_subject_to_tva' => 'required|boolean',
        ]);

        $user->agency->update([
            'commission_rate'   => $validated['agency_commission_rate'],
            'is_subject_to_tva' => $validated['agency_is_subject_to_tva'],
        ]);

        session()->flash('success_agency', 'Les paramètres financiers de l\'agence ont été enregistrés avec succès.');
    }

    public function updateProfileInformation(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($this->avatar) {
            // Supprimer l'ancien avatar si existant
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $this->avatar->store('avatars', 'public');
            $user->avatar_path = $path;
            $this->avatar = null;
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        session()->flash('success_profile', 'Vos informations de profil ont été mises à jour avec succès.');
    }

    public function removeAvatar(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        session()->flash('success_profile', 'Votre photo de profil a été supprimée.');
    }

    public function updatePassword(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Le mot de passe actuel saisi est incorrect.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('success_password', 'Votre mot de passe a été modifié avec succès.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profile.index', [
            'user' => Auth::user(),
        ]);
    }
}
