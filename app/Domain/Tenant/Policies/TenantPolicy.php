<?php

namespace App\Domain\Tenant\Policies;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('tenants.view');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.view')
            && $user->agency_id === $tenant->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('tenants.create');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.update')
            && $user->agency_id === $tenant->agency_id;
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.delete')
            && $user->agency_id === $tenant->agency_id;
    }
}
