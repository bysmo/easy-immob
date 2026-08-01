<?php

namespace App\Domain\Owner\Services;

use App\Domain\Agency\Models\Agency;
use App\Domain\Incident\Models\Incident;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Owner\Models\OwnerPayoutItem;
use App\Domain\Rent\Models\RentSchedule;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OwnerPayoutCalculatorService
{
    /**
     * Calcule et génère les factures de reversement pour une période donnée.
     *
     * @param Agency $agency
     * @param string $period Ex: "2026-07"
     * @param string $calculationType "collected" (loyers réellement encaissés) ou "expected" (loyers attendus)
     * @param int|null $ownerId ID d'un bailleur spécifique (facultatif)
     * @param User|null $creator
     * @return Collection<int, OwnerPayout>
     */
    public function calculateAndGeneratePayouts(
        Agency $agency,
        string $period,
        string $calculationType = 'collected',
        ?int $ownerId = null,
        ?User $creator = null
    ): Collection {
        return DB::transaction(function () use ($agency, $period, $calculationType, $ownerId, $creator) {
            $ownersQuery = Owner::where('agency_id', $agency->id)->where('status', 'active');
            if ($ownerId) {
                $ownersQuery->where('id', $ownerId);
            }

            $owners = $ownersQuery->get();
            $createdPayouts = collect();

            // Dériver les bornes du mois de la période (ex: "2026-07" → 2026-07-01 / 2026-07-31)
            [$periodYear, $periodMonth] = explode('-', $period);
            $periodStart = "{$periodYear}-{$periodMonth}-01";
            $periodEnd   = date('Y-m-t', mktime(0, 0, 0, (int) $periodMonth, 1, (int) $periodYear));

            foreach ($owners as $owner) {
                $propertyIds = $owner->properties()->pluck('id');
                if ($propertyIds->isEmpty()) {
                    continue;
                }

                // Récupération des échéances de la période
                $schedulesQuery = RentSchedule::with(['lease.property'])
                    ->where('agency_id', $agency->id)
                    ->whereHas('lease', fn ($q) => $q->whereIn('property_id', $propertyIds))
                    ->where('period', $period);

                if ($calculationType === 'collected') {
                    $schedulesQuery->where('paid_amount', '>', 0);
                }

                $schedules = $schedulesQuery->get();

                if ($schedules->isEmpty()) {
                    continue;
                }

                // Suppression d'un éventuel décompte "pending" existant pour ce bailleur et cette période
                $existingPending = OwnerPayout::where('agency_id', $agency->id)
                    ->where('owner_id', $owner->id)
                    ->where('period', $period)
                    ->where('status', 'pending')
                    ->first();

                if ($existingPending) {
                    $existingPending->items()->delete();
                    $existingPending->delete();
                }

                $totalGross      = 0.0;
                $totalCommission = 0.0;
                $totalIrf        = 0.0;
                $totalRepair     = 0.0;
                $itemsData       = [];

                foreach ($schedules as $schedule) {
                    $property = $schedule->lease?->property;
                    if (! $property) {
                        continue;
                    }

                    // Montant brut du loyer
                    $grossAmount = $calculationType === 'collected'
                        ? (float) $schedule->paid_amount
                        : (float) $schedule->expected_amount;

                    if ($grossAmount <= 0) {
                        continue;
                    }

                    // Commission agence sur le bien
                    $commRate = $property->agency_fee_value !== null
                        ? (float) $property->agency_fee_value
                        : (float) ($agency->commission_rate ?? 10.0);

                    if ($property->agency_fee_type === 'fixed') {
                        $commissionAmount = (float) $property->agency_fee_value;
                    } else {
                        $commissionAmount = round(($grossAmount * $commRate) / 100, 2);
                    }

                    // Impôt sur le Revenu Foncier (IRF) si applicable
                    $irfAmount = 0.0;
                    if ($property->is_subject_to_irf) {
                        $fullRentIrf = $property->irf_amount;
                        // Proportionnel si loyer partiel ou calcul au prorata
                        if ((float) $property->rent_amount > 0) {
                            $ratio     = min(1.0, $grossAmount / (float) $property->rent_amount);
                            $irfAmount = round($fullRentIrf * $ratio, 2);
                        }
                    }

                    // Réparations : incidents resolved ou closed sur ce bien dans la période
                    // On prend les incidents dont la date de résolution (resolved_at ou closed_at)
                    // tombe dans le mois de la période, avec un coût de réparation > 0.
                    $repairAmount = (float) Incident::where('agency_id', $agency->id)
                        ->where('property_id', $property->id)
                        ->where('repair_cost', '>', 0)
                        ->where(function ($q) use ($periodStart, $periodEnd) {
                            $q->whereBetween('resolved_at', [$periodStart, $periodEnd . ' 23:59:59'])
                              ->orWhereBetween('closed_at', [$periodStart, $periodEnd . ' 23:59:59']);
                        })
                        ->whereIn('status', ['resolved', 'closed'])
                        ->sum('repair_cost');

                    $repairAmount = round($repairAmount, 2);

                    $netAmount = max(0, round($grossAmount - $commissionAmount - $irfAmount - $repairAmount, 2));

                    $totalGross      += $grossAmount;
                    $totalCommission += $commissionAmount;
                    $totalIrf        += $irfAmount;
                    $totalRepair     += $repairAmount;

                    $itemsData[] = [
                        'property_id'       => $property->id,
                        'rent_schedule_id'  => $schedule->id,
                        'gross_amount'      => $grossAmount,
                        'commission_amount' => $commissionAmount,
                        'irf_amount'        => $irfAmount,
                        'repair_amount'     => $repairAmount,
                        'net_amount'        => $netAmount,
                        'description'       => "Loyer {$period} — Bien : {$property->title}",
                    ];
                }

                if ($totalGross <= 0) {
                    continue;
                }

                $totalNet  = max(0, round($totalGross - $totalCommission - $totalIrf - $totalRepair, 2));
                $reference = $this->generatePayoutReference($agency->id);

                /** @var OwnerPayout $payout */
                $payout = OwnerPayout::create([
                    'agency_id'               => $agency->id,
                    'owner_id'                => $owner->id,
                    'reference'               => $reference,
                    'period'                  => $period,
                    'calculation_type'        => $calculationType,
                    'gross_amount'            => $totalGross,
                    'commission_rate'         => (float) ($agency->commission_rate ?? 10.0),
                    'commission_amount'       => $totalCommission,
                    'irf_amount'              => $totalIrf,
                    'repair_amount'           => $totalRepair,
                    'other_deductions_amount' => 0,
                    'net_amount'              => $totalNet,
                    'paid_amount'             => 0,
                    'status'                  => 'pending',
                    'created_by'              => $creator?->id,
                ]);

                foreach ($itemsData as $item) {
                    $item['owner_payout_id'] = $payout->id;
                    OwnerPayoutItem::create($item);
                }

                $createdPayouts->push($payout);
            }

            return $createdPayouts;
        });
    }

    /**
     * Génère une référence séquentielle unique par agence : REV-2026-0001
     */
    private function generatePayoutReference(int $agencyId): string
    {
        $year   = date('Y');
        $prefix = "REV-{$year}-";

        $lastPayout = OwnerPayout::where('agency_id', $agencyId)
            ->where('reference', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastPayout) {
            return "{$prefix}0001";
        }

        $lastSeq = (int) substr($lastPayout->reference, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextSeq}";
    }
}
