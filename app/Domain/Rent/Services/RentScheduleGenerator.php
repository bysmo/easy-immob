<?php

namespace App\Domain\Rent\Services;

use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Carbon\Carbon;

class RentScheduleGenerator
{
    /**
     * Génère les échéances mensuelles de loyer pour la durée du contrat.
     */
    public function generateForLease(Lease $lease): void
    {
        $startDate = Carbon::parse($lease->start_date);
        $endDate   = Carbon::parse($lease->end_date);

        $current = $startDate->copy()->startOfMonth();

        while ($current->lessThanOrEqualTo($endDate)) {
            $period = $current->format('Y-m');

            // Calculer la date d'échéance selon payment_due_day
            $dueDay  = min($lease->payment_due_day, $current->daysInMonth);
            $dueDate = $current->copy()->day($dueDay);

            $totalAmount = $lease->total_monthly_amount;

            RentSchedule::firstOrCreate([
                'agency_id' => $lease->agency_id,
                'lease_id'  => $lease->id,
                'period'    => $period,
            ], [
                'due_date'         => $dueDate,
                'expected_amount'  => $totalAmount,
                'paid_amount'      => 0,
                'remaining_amount' => $totalAmount,
                'status'           => RentScheduleStatus::Pending,
            ]);

            $current->addMonth();
        }
    }
}
