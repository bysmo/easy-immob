<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('required|string|same:password')]
    public string $password_confirmation = '';

    #[Validate('required|string|exists:roles,name')]
    public string $role = '';

    /** @var array<string, string> */
    public array $availableRoles = [];

    public function mount(): void
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Rôles internes à l'agence uniquement.
        // Un Administrateur d'agence ne peut PAS créer de Super Admin.
        $excludedRoles = ['Propriétaire', 'Locataire'];

        if (! $currentUser->isSuperAdmin()) {
            $excludedRoles[] = 'Super Admin';
        }

        $this->availableRoles = Role::whereNotIn('name', $excludedRoles)
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }

    public function save(): void
    {
        $this->validate();

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Double vérification côté serveur : interdire Super Admin si non-SuperAdmin
        if ($this->role === 'Super Admin' && ! $currentUser->isSuperAdmin()) {
            $this->addError('role', 'Vous n\'êtes pas autorisé à assigner le rôle Super Admin.');
            return;
        }

        $user = User::withoutGlobalScopes()->create([
            'agency_id' => $currentUser->agency_id,
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($this->password),
        ]);

        $user->assignRole($this->role);

        session()->flash('success', "L'utilisateur {$user->name} a été créé avec le rôle {$this->role}.");

        $this->redirect(route('admin.users.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.users.create', [
            'availableRoles' => $this->availableRoles,
        ]);
    }
}
