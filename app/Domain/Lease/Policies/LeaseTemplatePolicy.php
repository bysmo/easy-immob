<?php

namespace App\Domain\Lease\Policies;

use App\Domain\Lease\Models\LeaseTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaseTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.view');
    }

    public function view(User $user, LeaseTemplate $template): bool
    {
        return $user->can('leases.view') && $user->agency_id === $template->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('leases.create');
    }

    public function update(User $user, LeaseTemplate $template): bool
    {
        return $user->can('leases.update') && $user->agency_id === $template->agency_id;
    }

    public function delete(User $user, LeaseTemplate $template): bool
    {
        return $user->can('leases.delete') && $user->agency_id === $template->agency_id;
    }
}
