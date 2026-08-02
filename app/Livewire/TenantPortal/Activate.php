<?php

namespace App\Livewire\TenantPortal;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Activation du compte portail locataire via Signed URL.
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
        if (! $user->hasRole('Locataire')) {
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

        session()->flash('success', 'Votre compte locataire est activé. Bienvenue sur votre espace !');

        $this->redirect(route('catalog.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenant-portal.activate');
    }
}
