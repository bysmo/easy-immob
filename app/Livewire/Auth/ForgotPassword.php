<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    public bool $sent = false;

    public function sendLink(): void
    {
        $this->validate();

        // Toujours afficher "lien envoyé" même si l'email n'existe pas
        // pour ne pas divulguer l'existence d'un compte (sécurité)
        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.forgot-password');
    }
}
