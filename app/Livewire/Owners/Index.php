<?php

namespace App\Livewire\Owners;

use App\Domain\Owner\Models\Owner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $ownerId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $owner = Owner::where('id', $ownerId)->firstOrFail();
        $this->authorize('delete', $owner);

        $owner->delete();

        session()->flash('success', "Le propriétaire {$owner->full_name} a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        $owners = Owner::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.owners.index', compact('owners'));
    }
}
