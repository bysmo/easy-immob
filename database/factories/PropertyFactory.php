<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'        => $agency->id,
            'reference'        => 'BIE-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'owner_id'         => Owner::factory()->for($agency, 'agency'),
            'property_type_id' => PropertyType::factory()->for($agency, 'agency'),
            'title'            => $this->faker->sentence(3),
            'description'      => $this->faker->paragraph(),
            'address'          => $this->faker->streetAddress(),
            'city'             => $this->faker->city(),
            'neighborhood'     => $this->faker->word(),
            'surface_area'     => $this->faker->randomFloat(2, 20, 300),
            'bedrooms'         => $this->faker->numberBetween(1, 5),
            'bathrooms'        => $this->faker->numberBetween(1, 3),
            'rent_amount'      => $this->faker->randomFloat(2, 50000, 500000),
            'status'           => PropertyStatus::Available,
        ];
    }

    public function occupied(): static
    {
        return $this->state(['status' => PropertyStatus::Occupied]);
    }

    public function maintenance(): static
    {
        return $this->state(['status' => PropertyStatus::Maintenance]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => PropertyStatus::Inactive]);
    }
}
