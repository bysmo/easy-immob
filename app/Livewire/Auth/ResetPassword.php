<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResetPassword extends Component
{
    #[Url]
    public string $token = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|min:8|same:password_confirmation')]
    public string $password = '';

    #[Validate('required|string')]
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email')->toString();
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.');
            $this->redirect(route('login'), navigate: false);
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.reset-password');
    }
}
