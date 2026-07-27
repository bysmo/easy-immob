<?php

namespace App\Domain\Report\Services;

use App\Domain\Payment\Models\Payment;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialReportService
{
    /**
     * Retourne la synthèse financière pour une agence donnée et une plage de dates.
     *
     * @return array{
     *     expected_total: float,
     *     collected_total: float,
     *     remaining_total: float,
     *     collection_rate: float,
     *     schedules_count: int,
     *     paid_schedules_count: int,
     *     overdue_schedules_count: int
     * }
     */
    public function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = RentSchedule::query();

        if ($startDate && $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        $expectedTotal  = (float) $query->sum('expected_amount');
        $collectedTotal = (float) $query->sum('paid_amount');
        $remainingTotal = (float) $query->sum('remaining_amount');

        $collectionRate = $expectedTotal > 0
            ? round(($collectedTotal / $expectedTotal) * 100, 2)
            : 0;

        $schedulesCount        = $query->count();
        $paidSchedulesCount    = (clone $query)->where('status', RentScheduleStatus::Paid)->count();
        $overdueSchedulesCount = (clone $query)->where('status', RentScheduleStatus::Overdue)->count();

        return [
            'expected_total'          => $expectedTotal,
            'collected_total'         => $collectedTotal,
            'remaining_total'         => $remainingTotal,
            'collection_rate'         => $collectionRate,
            'schedules_count'         => $schedulesCount,
            'paid_schedules_count'     => $paidSchedulesCount,
            'overdue_schedules_count' => $overdueSchedulesCount,
        ];
    }
}
