<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Réinitialise la pagination quand la recherche change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $users = User::withoutGlobalScopes()
            ->with('roles')
            ->where('agency_id', $currentUser->agency_id)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users.index', compact('users'));
    }
}
