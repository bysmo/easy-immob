<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public User $user;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|exists:roles,name')]
    public string $role = '';

    /** @var array<string, string> */
    public array $availableRoles = [];

    public function mount(int $userId): void
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // S'assurer que l'utilisateur appartient bien à la même agence
        $this->user = User::withoutGlobalScopes()
            ->where('agency_id', $currentUser->agency_id)
            ->where('id', $userId)
            ->first() ?? abort(404);

        $this->name  = $this->user->name;
        $this->email = $this->user->email;
        $this->role  = $this->user->getRoleNames()->first() ?? '';

        // Rôles disponibles : exclure les rôles non-agence et, pour un non-SuperAdmin, exclure Super Admin
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
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
            'role'  => 'required|string|exists:roles,name',
        ]);

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Double vérification côté serveur : interdire Super Admin si non-SuperAdmin
        if ($this->role === 'Super Admin' && ! $currentUser->isSuperAdmin()) {
            $this->addError('role', 'Vous n\'êtes pas autorisé à assigner le rôle Super Admin.');
            return;
        }

        $this->user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->user->syncRoles([$this->role]);

        session()->flash('success', "L'utilisateur {$this->user->name} a été mis à jour avec le rôle {$this->role}.");

        $this->redirect(route('admin.users.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.users.edit', [
            'availableRoles' => $this->availableRoles,
        ]);
    }
}
