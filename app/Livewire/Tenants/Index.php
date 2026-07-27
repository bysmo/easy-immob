<?php

namespace App\Livewire\Tenants;

use App\Domain\Tenant\Models\Tenant;
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

    public function delete(int $tenantId): void
    {
        $tenant = Tenant::where('id', $tenantId)->firstOrFail();
        $this->authorize('delete', $tenant);

        $tenant->delete();

        session()->flash('success', "Le locataire {$tenant->full_name} a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        $tenants = Tenant::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.tenants.index', compact('tenants'));
    }
}
