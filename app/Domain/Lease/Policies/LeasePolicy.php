<?php

namespace App\Domain\Lease\Policies;

use App\Domain\Lease\Models\Lease;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.view');
    }

    public function view(User $user, Lease $lease): bool
    {
        return $user->can('leases.view') && $user->agency_id === $lease->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('leases.create');
    }

    public function update(User $user, Lease $lease): bool
    {
        return $user->can('leases.update') && $user->agency_id === $lease->agency_id;
    }

    public function delete(User $user, Lease $lease): bool
    {
        return $user->can('leases.delete') && $user->agency_id === $lease->agency_id;
    }
}
