<?php

namespace App\Domain\Lease\Actions;

use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Rent\Services\RentScheduleGenerator;
use Illuminate\Support\Facades\DB;
use ValidationException;

class ActivateLeaseAction
{
    public function __construct(
        private RentScheduleGenerator $rentScheduleGenerator
    ) {}

    public function execute(Lease $lease): Lease
    {
        return DB::transaction(function () use ($lease) {
            // 1. Vérifier le chevauchement de dates avec d'autres contrats actifs sur la même propriété
            $overlapping = Lease::where('property_id', $lease->property_id)
                ->where('id', '!=', $lease->id)
                ->where('status', LeaseStatus::Active)
                ->where(function ($query) use ($lease) {
                    $query->whereBetween('start_date', [$lease->start_date, $lease->end_date])
                        ->orWhereBetween('end_date', [$lease->start_date, $lease->end_date])
                        ->orWhere(function ($q) use ($lease) {
                            $q->where('start_date', '<=', $lease->start_date)
                                ->where('end_date', '>=', $lease->end_date);
                        });
                })
                ->exists();

            if ($overlapping) {
                throw new \InvalidArgumentException("Ce bien a déjà un contrat actif qui se chevauche sur cette période.");
            }

            // 2. Activer le contrat
            $lease->update([
                'status'    => LeaseStatus::Active,
                'signed_at' => $lease->signed_at ?? now(),
            ]);

            // 3. Basculer le statut du bien en occupé
            $lease->property->update([
                'status' => PropertyStatus::Occupied,
            ]);

            // 4. Générer automatiquement les échéances de loyer
            $this->rentScheduleGenerator->generateForLease($lease);

            return $lease->fresh();
        });
    }
}
