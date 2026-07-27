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

        $this->availableRoles = Role::whereNotIn('name', ['Propriétaire', 'Locataire'])
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

        $this->user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->user->syncRoles([$this->role]);

        session()->flash('success', "L'utilisateur {$this->user->name} a été mis à jour.");

        $this->redirect(route('admin.users.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.users.edit', [
            'availableRoles' => $this->availableRoles,
        ]);
    }
}
