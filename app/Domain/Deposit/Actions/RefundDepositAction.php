<?php

namespace App\Domain\Deposit\Actions;

use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use Illuminate\Support\Facades\DB;

class RefundDepositAction
{
    public function execute(
        Deposit $deposit,
        float $refundedAmount,
        float $retainedAmount,
        ?string $retentionReason,
        string $refundedAt
    ): Deposit {
        if ($retainedAmount > 0 && empty(trim((string) $retentionReason))) {
            throw new \InvalidArgumentException("Toute retenue sur la caution doit obligatoirement être motivée.");
        }

        return DB::transaction(function () use ($deposit, $refundedAmount, $retainedAmount, $retentionReason, $refundedAt) {
            $status = DepositStatus::Refunded;
            if ($retainedAmount > 0 && $refundedAmount > 0) {
                $status = DepositStatus::PartiallyRefunded;
            } elseif ($retainedAmount > 0 && $refundedAmount == 0) {
                $status = DepositStatus::Forfeited;
            }

            $deposit->update([
                'refunded_amount'  => $refundedAmount,
                'retained_amount'  => $retainedAmount,
                'retention_reason' => $retentionReason,
                'refunded_at'      => $refundedAt,
                'status'           => $status,
            ]);

            return $deposit->fresh();
        });
    }
}
