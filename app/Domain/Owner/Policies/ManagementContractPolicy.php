<?php

namespace App\Domain\Owner\Policies;

use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManagementContractPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('management_contracts.view') || $user->isOwner();
    }

    public function view(User $user, ManagementContract $contract): bool
    {
        if ($user->isOwner()) {
            return $user->owner?->id === $contract->owner_id;
        }

        return $user->can('management_contracts.view')
            && $user->agency_id === $contract->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('management_contracts.create');
    }

    public function update(User $user, ManagementContract $contract): bool
    {
        return $user->can('management_contracts.update')
            && $user->agency_id === $contract->agency_id;
    }

    public function delete(User $user, ManagementContract $contract): bool
    {
        // Un mandat actif ne peut pas être supprimé
        if ($contract->status === ManagementContractStatus::Active) {
            return false;
        }

        return $user->can('management_contracts.delete')
            && $user->agency_id === $contract->agency_id;
    }
}
