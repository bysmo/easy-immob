<?php

namespace App\Domain\Deposit\Policies;

use App\Domain\Deposit\Models\Deposit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepositPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('deposits.view');
    }

    public function view(User $user, Deposit $deposit): bool
    {
        return $user->can('deposits.view') && $user->agency_id === $deposit->agency_id;
    }

    public function manage(User $user, Deposit $deposit): bool
    {
        return $user->can('deposits.manage') && $user->agency_id === $deposit->agency_id;
    }
}
