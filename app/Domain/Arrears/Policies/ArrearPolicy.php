<?php

namespace App\Domain\Arrears\Policies;

use App\Domain\Arrears\Models\Arrear;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArrearPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('arrears.view');
    }

    public function view(User $user, Arrear $arrear): bool
    {
        return $user->can('arrears.view') && $user->agency_id === $arrear->agency_id;
    }

    public function manage(User $user, Arrear $arrear): bool
    {
        return $user->can('arrears.manage') && $user->agency_id === $arrear->agency_id;
    }
}
