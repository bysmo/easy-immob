<?php

namespace App\Livewire\ManagementContracts;

use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $ownerFilter = null;

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'ownerFilter'  => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ManagementContract::with(['owner', 'properties'])
            ->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('owner', function ($oq) {
                      $oq->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->ownerFilter) {
            $query->where('owner_id', $this->ownerFilter);
        }

        $contracts = $query->paginate(10);
        $owners    = Owner::orderBy('last_name')->get();

        return view('livewire.management-contracts.index', [
            'contracts' => $contracts,
            'owners'    => $owners,
        ])->layout('components.layouts.app');
    }
}
