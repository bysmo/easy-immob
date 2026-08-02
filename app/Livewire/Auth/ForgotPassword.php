<?php

namespace App\Livewire\Auth;

use App\Application\Services\DynamicMailConfigurator;
use Illuminate\Support\Facades\Log;
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

        try {
            DynamicMailConfigurator::apply();
            Password::sendResetLink(['email' => $this->email]);
        } catch (\Throwable $e) {
            Log::error("Erreur réinitialisation mot de passe: " . $e->getMessage());
        }

        // Toujours afficher "lien envoyé" même si l'email n'existe pas ou en cas de problème réseau
        // pour des raisons de sécurité et de robustesse
        $this->sent = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.forgot-password');
    }
}
