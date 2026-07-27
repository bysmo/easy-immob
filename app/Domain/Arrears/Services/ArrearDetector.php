<?php

namespace App\Domain\Arrears\Services;

use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Carbon\Carbon;

class ArrearDetector
{
    /**
     * Balaye les échéances échues non soldées et crée/met à jour les dossiers d'impayés.
     */
    public function detect(): int
    {
        $today = Carbon::today();

        $overdueSchedules = RentSchedule::with('lease')
            ->where('due_date', '<', $today)
            ->where('remaining_amount', '>', 0)
            ->get();

        $count = 0;

        foreach ($overdueSchedules as $schedule) {
            $daysOverdue = (int) $schedule->due_date->diffInDays($today);

            // Calcul de la sévérité
            $severity = match (true) {
                $daysOverdue >= 15 => ArrearSeverity::Critical,
                $daysOverdue >= 7  => ArrearSeverity::Serious,
                default            => ArrearSeverity::Warning,
            };

            // Mettre à jour le statut de l'échéance à overdue
            if ($schedule->status !== RentScheduleStatus::Overdue) {
                $schedule->update(['status' => RentScheduleStatus::Overdue]);
            }

            // Créer ou mettre à jour le dossier d'impayé
            Arrear::withoutGlobalScopes()->updateOrCreate([
                'agency_id'        => $schedule->agency_id,
                'rent_schedule_id' => $schedule->id,
            ], [
                'lease_id'           => $schedule->lease_id,
                'tenant_id'          => $schedule->lease->tenant_id,
                'amount_due'         => $schedule->expected_amount,
                'amount_paid'        => $schedule->paid_amount,
                'remaining_amount'   => $schedule->remaining_amount,
                'first_overdue_date' => $schedule->due_date,
                'severity'           => $severity,
                'status'             => ArrearStatus::Open,
            ]);

            $count++;
        }

        // Mettre à jour les dossiers d'impayés pour les échéances soldées
        $settledSchedules = RentSchedule::where('remaining_amount', 0)->pluck('id');
        Arrear::withoutGlobalScopes()
            ->whereIn('rent_schedule_id', $settledSchedules)
            ->where('status', '!=', ArrearStatus::Settled)
            ->update(['status' => ArrearStatus::Settled]);

        return $count;
    }
}
