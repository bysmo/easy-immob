<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'         => $agency->id,
            'reference'         => 'LOC-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name'        => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'email'             => $this->faker->unique()->safeEmail(),
            'phone'             => $this->faker->phoneNumber(),
            'address'           => $this->faker->address(),
            'emergency_contact' => $this->faker->name() . ' (' . $this->faker->phoneNumber() . ')',
            'status'            => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
