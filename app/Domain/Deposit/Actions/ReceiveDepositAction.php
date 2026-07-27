<?php

namespace App\Domain\Deposit\Actions;

use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use Illuminate\Support\Facades\DB;

class ReceiveDepositAction
{
    public function execute(Deposit $deposit, float $amount, string $receivedAt): Deposit
    {
        return DB::transaction(function () use ($deposit, $amount, $receivedAt) {
            $deposit->update([
                'received_amount' => $amount,
                'received_at'     => $receivedAt,
                'status'          => DepositStatus::Held,
            ]);

            return $deposit->fresh();
        });
    }
}
