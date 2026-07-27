<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Property\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyType>
 */
class PropertyTypeFactory extends Factory
{
    protected $model = PropertyType::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'   => $agency->id,
            'name'        => $this->faker->randomElement(['Appartement', 'Maison', 'Villa', 'Studio', 'Bureau', 'Magasin', 'Terrain', 'Entrepôt']),
            'description' => $this->faker->optional()->sentence(),
            'status'      => 'active',
        ];
    }
}
