<?php

namespace App\Livewire\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use App\Application\Actions\Auth\RegisterTenantAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $account_type = 'agency'; // agency or tenant

    // Agency fields
    public string $agency_name = '';
    public string $name = '';

    // Tenant fields
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';

    // Shared fields
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function setAccountType(string $type): void
    {
        if (in_array($type, ['agency', 'tenant'])) {
            $this->account_type = $type;
            $this->resetValidation();
        }
    }

    public function register(RegisterAgencyAction $agencyAction, RegisterTenantAction $tenantAction): void
    {
        if ($this->account_type === 'agency') {
            $this->validate([
                'agency_name'           => 'required|string|max:255',
                'name'                  => 'required|string|max:255',
                'email'                 => 'required|email|max:255|unique:users,email|unique:agencies,email',
                'password'              => 'required|string|min:8',
                'password_confirmation' => 'required|string|same:password',
            ]);

            $user = $agencyAction->create([
                'agency_name'           => $this->agency_name,
                'name'                  => $this->name,
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);
        } else {
            $this->validate([
                'first_name'            => 'required|string|max:255',
                'last_name'             => 'required|string|max:255',
                'email'                 => 'required|email|max:255|unique:users,email',
                'phone'                 => 'nullable|string|max:255',
                'password'              => 'required|string|min:8',
                'password_confirmation' => 'required|string|same:password',
            ]);

            $user = $tenantAction->create([
                'first_name'            => $this->first_name,
                'last_name'             => $this->last_name,
                'email'                 => $this->email,
                'phone'                 => $this->phone,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);
        }

        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
