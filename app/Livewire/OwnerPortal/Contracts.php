<?php

namespace App\Livewire\OwnerPortal;

use App\Domain\Owner\Actions\RevokeManagementContract;
use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Mandats de gestion du bailleur avec possibilité de résiliation immédiate.
 */
class Contracts extends Component
{
    public bool $showRevokeModal = false;
    public ?int $revokeContractId = null;
    public string $revokeContractRef = '';

    public function openRevokeModal(int $contractId): void
    {
        $contract = ManagementContract::withoutGlobalScopes()->findOrFail($contractId);
        $this->authorizeContract($contract);

        $this->revokeContractId  = $contractId;
        $this->revokeContractRef = $contract->reference;
        $this->showRevokeModal   = true;
    }

    public function revokeContract(RevokeManagementContract $action): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $contract = ManagementContract::withoutGlobalScopes()->findOrFail($this->revokeContractId);
        $this->authorizeContract($contract);

        try {
            $action->execute($contract, $user);
            session()->flash('success', "Le mandat #{$contract->reference} a été résilié.");
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showRevokeModal = false;
    }

    private function authorizeContract(ManagementContract $contract): void
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        if (! $owner || $contract->owner_id !== $owner->id) {
            abort(403);
        }
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        if (! $owner) {
            abort(403);
        }

        $contracts = ManagementContract::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->with(['agency', 'properties'])
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.owner-portal.contracts', [
            'owner'     => $owner,
            'contracts' => $contracts,
            'statuses'  => ManagementContractStatus::cases(),
        ]);
    }
}
