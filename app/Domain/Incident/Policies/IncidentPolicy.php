<?php

namespace App\Domain\Incident\Policies;

use App\Domain\Incident\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('incidents.view') || $user->hasRole('Locataire');
    }

    public function view(User $user, Incident $incident): bool
    {
        if ($user->hasRole('Locataire')) {
            return $user->tenant?->id === $incident->tenant_id;
        }

        return $user->can('incidents.view') && ($user->agency_id === null || $user->agency_id === $incident->agency_id);
    }

    public function create(User $user): bool
    {
        return $user->can('incidents.create') || $user->hasRole('Locataire');
    }

    public function update(User $user, Incident $incident): bool
    {
        if ($user->hasRole('Locataire')) {
            return $user->tenant?->id === $incident->tenant_id;
        }

        return $user->can('incidents.update') && ($user->agency_id === null || $user->agency_id === $incident->agency_id);
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->can('incidents.manage') && ($user->agency_id === null || $user->agency_id === $incident->agency_id);
    }
}
