<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Property\Models\PropertyType;
use Illuminate\Database\Seeder;

class ReferentialSeeder extends Seeder
{
    public const DEFAULT_PROPERTY_TYPES = [
        'Appartement',
        'Maison',
        'Villa',
        'Studio',
        'Bureau',
        'Magasin',
        'Terrain',
        'Entrepôt',
    ];

    public function run(): void
    {
        $agencies = Agency::all();

        foreach ($agencies as $agency) {
            foreach (self::DEFAULT_PROPERTY_TYPES as $typeName) {
                PropertyType::withoutGlobalScopes()->firstOrCreate([
                    'agency_id' => $agency->id,
                    'name'      => $typeName,
                ], [
                    'status'    => 'active',
                ]);
            }
        }
    }
}
