<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Property\Models\Property;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lease>
 */
class LeaseFactory extends Factory
{
    protected $model = Lease::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'       => $agency->id,
            'reference'       => 'CON-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'property_id'     => Property::factory()->for($agency, 'agency'),
            'tenant_id'       => Tenant::factory()->for($agency, 'agency'),
            'template_id'     => LeaseTemplate::factory()->for($agency, 'agency'),
            'start_date'      => now()->startOfMonth()->format('Y-m-d'),
            'end_date'        => now()->addYear()->endOfMonth()->format('Y-m-d'),
            'rent_amount'     => 150000,
            'charges_amount'  => 10000,
            'payment_due_day'  => 5,
            'is_tacit_renewal' => true,
            'deposit_amount'   => 300000,
            'status'          => LeaseStatus::Draft,
            'signed_at'       => null,
            'terminated_at'   => null,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status'    => LeaseStatus::Active,
            'signed_at' => now(),
        ]);
    }

    public function terminated(): static
    {
        return $this->state([
            'status'        => LeaseStatus::Terminated,
            'signed_at'     => now()->subYear(),
            'terminated_at' => now(),
        ]);
    }
}
