<?php

namespace App\Livewire\Tenants;

use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';

    public function updatedStatusFilter(): void
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
        $query = Tenant::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $tenants = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.tenants.index', compact('tenants'));
    }
}
