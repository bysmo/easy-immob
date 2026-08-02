<?php

namespace App\Livewire\OwnerPortal;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Activation du compte portail bailleur via Signed URL.
 * Le middleware `signed` sur la route garantit l'intégrité et l'expiration du lien.
 */
class Activate extends Component
{
    public User $user;

    #[Validate('required|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(User $user): void
    {
        // Vérifier que cet utilisateur a bien le rôle Bailleur
        if (! $user->hasRole('Bailleur')) {
            abort(403, 'Lien d\'activation invalide.');
        }

        $this->user = $user;
    }

    public function activate(): void
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $this->user->update([
            'password'          => Hash::make($this->password),
            'email_verified_at' => $this->user->email_verified_at ?? now(),
        ]);

        Auth::login($this->user);

        session()->flash('success', 'Votre compte bailleur est activé. Bienvenue !');

        $this->redirect(route('owner-portal.dashboard'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.owner-portal.activate');
    }
}
