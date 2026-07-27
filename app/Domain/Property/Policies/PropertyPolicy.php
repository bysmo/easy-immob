<?php

namespace App\Domain\Property\Policies;

use App\Domain\Property\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('properties.view');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->can('properties.view')
            && $user->agency_id === $property->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('properties.create');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->can('properties.update')
            && $user->agency_id === $property->agency_id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->can('properties.delete')
            && $user->agency_id === $property->agency_id;
    }
}
