<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Arrear>
 */
class ArrearFactory extends Factory
{
    protected $model = Arrear::class;

    public function definition(): array
    {
        $agency   = Agency::factory()->create();
        $lease    = Lease::factory()->for($agency, 'agency')->create();
        $schedule = RentSchedule::factory()->for($agency, 'agency')->create(['lease_id' => $lease->id]);

        return [
            'agency_id'          => $agency->id,
            'lease_id'           => $lease->id,
            'rent_schedule_id'   => $schedule->id,
            'tenant_id'          => $lease->tenant_id,
            'amount_due'         => 160000,
            'amount_paid'        => 0,
            'remaining_amount'   => 160000,
            'first_overdue_date' => now()->subDays(5)->format('Y-m-d'),
            'severity'           => ArrearSeverity::Warning,
            'status'             => ArrearStatus::Open,
        ];
    }
}
