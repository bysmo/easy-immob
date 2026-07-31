<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agency>
 */
class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company().' SARL',
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address'           => fake()->address(),
            'commission_rate'   => 10.00,
            'is_subject_to_tva' => true,
            'status'            => 'active',
        ];
    }
}
