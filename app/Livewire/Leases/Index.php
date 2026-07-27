<?php

namespace App\Livewire\Leases;

use App\Domain\Lease\Actions\ActivateLeaseAction;
use App\Domain\Lease\Actions\TerminateLeaseAction;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function activate(int $leaseId, ActivateLeaseAction $action): void
    {
        $lease = Lease::where('id', $leaseId)->firstOrFail();
        $this->authorize('update', $lease);

        try {
            $action->execute($lease);
            session()->flash('success', "Le contrat {$lease->reference} a été activé avec succès. Le bien est à présent occupé et les échéances ont été générées.");
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function terminate(int $leaseId, TerminateLeaseAction $action): void
    {
        $lease = Lease::where('id', $leaseId)->firstOrFail();
        $this->authorize('update', $lease);

        $action->execute($lease);
        session()->flash('success', "Le contrat {$lease->reference} a été résilié. Le bien a été libéré.");
    }

    public function render(): \Illuminate\View\View
    {
        $leases = Lease::with(['property', 'tenant'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('tenant', function ($tenantQ) {
                            $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('property', function ($propQ) {
                            $propQ->where('title', 'like', '%' . $this->search . '%')
                                ->orWhere('reference', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.leases.index', [
            'leases'        => $leases,
            'statusOptions' => LeaseStatus::options(),
        ]);
    }
}
