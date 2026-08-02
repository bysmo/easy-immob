<?php

namespace App\Domain\Owner\Policies;

use App\Domain\Owner\Models\Owner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OwnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('owners.view');
    }

    public function view(User $user, Owner $owner): bool
    {
        return $user->can('owners.view')
            && $user->agency_id === $owner->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('owners.create');
    }

    public function update(User $user, Owner $owner): bool
    {
        if ($owner->hasPortalAccess()) {
            return false;
        }

        return $user->can('owners.update')
            && $user->agency_id === $owner->agency_id;
    }

    public function delete(User $user, Owner $owner): bool
    {
        if ($owner->hasPortalAccess()) {
            return false;
        }

        return $user->can('owners.delete')
            && $user->agency_id === $owner->agency_id;
    }
}
