<?php

namespace Tests\Feature\Domain\Agency;

use App\Domain\Agency\Models\Agency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created_with_expected_attributes(): void
    {
        $agency = Agency::create([
            'name' => 'Agence du Plateau',
            'legal_name' => 'Agence du Plateau SARL',
            'email' => 'contact@plateau.example',
            'phone' => '+225 07 00 00 00 00',
            'address' => 'Abidjan, Plateau',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Agence du Plateau',
            'status' => 'active',
        ]);
    }
}
