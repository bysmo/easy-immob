<?php

namespace App\Domain\Lease\Actions;

use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Property\Enums\PropertyStatus;
use Illuminate\Support\Facades\DB;

class TerminateLeaseAction
{
    public function execute(Lease $lease): Lease
    {
        return DB::transaction(function () use ($lease) {
            $lease->update([
                'status'        => LeaseStatus::Terminated,
                'terminated_at' => now(),
            ]);

            // Vérifier s'il reste d'autres contrats actifs sur ce bien
            $hasOtherActiveLeases = Lease::where('property_id', $lease->property_id)
                ->where('id', '!=', $lease->id)
                ->where('status', LeaseStatus::Active)
                ->exists();

            if (! $hasOtherActiveLeases) {
                $lease->property->update([
                    'status' => PropertyStatus::Available,
                ]);
            }

            return $lease->fresh();
        });
    }
}
