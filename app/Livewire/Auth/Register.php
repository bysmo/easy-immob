<?php

namespace App\Livewire\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $agency_name = '';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('required|string|same:password')]
    public string $password_confirmation = '';

    public function register(RegisterAgencyAction $action): void
    {
        $this->validate();

        $user = $action->create([
            'agency_name'           => $this->agency_name,
            'name'                  => $this->name,
            'email'                 => $this->email,
            'password'              => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
