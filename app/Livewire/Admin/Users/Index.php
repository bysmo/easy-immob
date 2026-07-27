<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Traits\WithDataTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $query = User::withoutGlobalScopes()
            ->with('roles')
            ->where('agency_id', $currentUser->agency_id)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            }));

        $users = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.admin.users.index', compact('users'));
    }
}
