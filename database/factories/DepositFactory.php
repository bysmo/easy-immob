<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use App\Domain\Lease\Models\Lease;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposit>
 */
class DepositFactory extends Factory
{
    protected $model = Deposit::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'        => $agency->id,
            'lease_id'         => Lease::factory()->for($agency, 'agency'),
            'expected_amount'  => 300000,
            'received_amount'  => 0,
            'received_at'      => null,
            'retained_amount'  => 0,
            'retention_reason' => null,
            'refunded_amount'  => 0,
            'refunded_at'      => null,
            'status'           => DepositStatus::Pending,
        ];
    }

    public function held(): static
    {
        return $this->state([
            'received_amount' => 300000,
            'received_at'     => now()->format('Y-m-d'),
            'status'          => DepositStatus::Held,
        ]);
    }
}
