<?php

namespace App\Domain\Report\Services;

use App\Domain\Owner\Models\Owner;
use App\Domain\Rent\Models\RentSchedule;

class OwnerStatementService
{
    /**
     * Génère le relevé financier d'un propriétaire avec le détail de ses biens,
     * les encaissements perçus, les frais de gestion agence et le montant net à reverser.
     *
     * @return array{
     *     owner: Owner,
     *     properties_count: int,
     *     total_collected: float,
     *     management_fee_percentage: float,
     *     management_fee_amount: float,
     *     net_payable: float,
     *     schedules: \Illuminate\Database\Eloquent\Collection
     * }
     */
    public function generateStatement(Owner $owner, float $managementFeePercentage = 8.0, ?string $period = null): array
    {
        $propertyIds = $owner->properties()->pluck('id');

        $query = RentSchedule::with(['lease.property', 'lease.tenant'])
            ->whereHas('lease', fn ($q) => $q->whereIn('property_id', $propertyIds));

        if ($period) {
            $query->where('period', $period);
        }

        $schedules = $query->get();

        $totalCollected = (float) $schedules->sum('paid_amount');
        $managementFeeAmount = round(($totalCollected * $managementFeePercentage) / 100, 2);
        $netPayable = max(0, $totalCollected - $managementFeeAmount);

        return [
            'owner'                     => $owner,
            'properties_count'          => $propertyIds->count(),
            'total_collected'           => $totalCollected,
            'management_fee_percentage' => $managementFeePercentage,
            'management_fee_amount'     => $managementFeeAmount,
            'net_payable'               => $netPayable,
            'schedules'                 => $schedules,
        ];
    }
}
