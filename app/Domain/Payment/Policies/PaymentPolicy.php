<?php

namespace App\Domain\Payment\Policies;

use App\Domain\Payment\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('rents.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('rents.view') && $user->agency_id === $payment->agency_id;
    }

    public function create(User $user): bool
    {
        return $user->can('rents.record-payment');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('rents.record-payment') && $user->agency_id === $payment->agency_id;
    }
}
