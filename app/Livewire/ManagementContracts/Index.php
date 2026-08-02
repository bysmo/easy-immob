<?php

namespace App\Livewire\ManagementContracts;

use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Livewire\Traits\WithDataTable;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';
    public ?int $ownerFilter = null;

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $contractId): void
    {
        $contract = ManagementContract::findOrFail($contractId);

        if ($contract->status === ManagementContractStatus::Active) {
            session()->flash('error', "Un mandat de gestion actif ne peut pas être supprimé. Veuillez le résilier ou attendre son expiration.");
            return;
        }

        $this->authorize('delete', $contract);

        $contract->delete();

        session()->flash('success', "Le mandat de gestion {$contract->reference} a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        $query = ManagementContract::with(['owner', 'properties'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('owner', function ($oq) {
                            $oq->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                ->orWhere('company_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->ownerFilter, function ($q) {
                $q->where('owner_id', $this->ownerFilter);
            });

        $contracts = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);
        $owners = Owner::orderBy('last_name')->get();

        return view('livewire.management-contracts.index', [
            'contracts'     => $contracts,
            'owners'        => $owners,
            'statusOptions' => ManagementContractStatus::options(),
        ]);
    }
}
