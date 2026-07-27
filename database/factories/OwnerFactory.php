<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Owner>
 */
class OwnerFactory extends Factory
{
    protected $model = Owner::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'    => $agency->id,
            'reference'    => 'PRO-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name'   => $this->faker->firstName(),
            'last_name'    => $this->faker->lastName(),
            'company_name' => null,
            'email'        => $this->faker->unique()->safeEmail(),
            'phone'        => $this->faker->phoneNumber(),
            'address'      => $this->faker->address(),
            'status'       => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function company(): static
    {
        return $this->state(['company_name' => $this->faker->company()]);
    }
}
