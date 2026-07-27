<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RentSchedule>
 */
class RentScheduleFactory extends Factory
{
    protected $model = RentSchedule::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'        => $agency->id,
            'lease_id'         => Lease::factory()->for($agency, 'agency'),
            'period'           => now()->format('Y-m'),
            'due_date'         => now()->day(5)->format('Y-m-d'),
            'expected_amount'  => 160000,
            'paid_amount'      => 0,
            'remaining_amount' => 160000,
            'status'           => RentScheduleStatus::Pending,
        ];
    }
}
