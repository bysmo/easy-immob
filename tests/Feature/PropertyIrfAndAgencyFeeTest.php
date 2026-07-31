<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Rent\Models\RentSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyIrfAndAgencyFeeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->agency = Agency::factory()->create([
            'commission_rate'   => 10.00,
            'is_subject_to_tva' => true,
        ]);

        $this->user = User::factory()->create([
            'agency_id' => $this->agency->id,
        ]);
        $this->user->assignRole('Administrateur');
    }

    public function test_rents_index_defaults_to_current_month_period_filter(): void
    {
        $this->actingAs($this->user);

        // Current month schedule
        $currentMonthSchedule = RentSchedule::factory()->create([
            'agency_id' => $this->agency->id,
            'period'    => now()->format('Y-m'),
        ]);

        // Past month schedule
        $pastMonthSchedule = RentSchedule::factory()->create([
            'agency_id' => $this->agency->id,
            'period'    => now()->subMonths(3)->format('Y-m'),
        ]);

        Livewire::test(\App\Livewire\Rents\Index::class)
            ->assertSet('periodFilter', now()->format('Y-m'))
            ->assertViewHas('schedules', function ($schedules) use ($currentMonthSchedule, $pastMonthSchedule) {
                return $schedules->pluck('id')->contains($currentMonthSchedule->id)
                    && ! $schedules->pluck('id')->contains($pastMonthSchedule->id);
            });
    }

    public function test_irf_calculation_for_burkina_faso_progressive_scale(): void
    {
        // 1st tranche: rent <= 100 000 FCFA => 18%
        $prop1 = new Property([
            'rent_amount'       => 80000,
            'is_subject_to_irf' => true,
        ]);
        $this->assertEquals(14400.00, $prop1->irf_amount);

        // 2nd tranche: rent > 100 000 FCFA => 18% on first 100k (18 000) + 25% on portion > 100k
        $prop2 = new Property([
            'rent_amount'       => 250000,
            'is_subject_to_irf' => true,
        ]);
        // 18 000 + (150 000 * 0.25) = 18 000 + 37 500 = 55 500 FCFA
        $this->assertEquals(55500.00, $prop2->irf_amount);

        // If IRF not checked => 0
        $propExempt = new Property([
            'rent_amount'       => 250000,
            'is_subject_to_irf' => false,
        ]);
        $this->assertEquals(0.0, $propExempt->irf_amount);
    }

    public function test_agency_fee_calculation_percentage_and_fixed(): void
    {
        $propertyType = PropertyType::factory()->create(['agency_id' => $this->agency->id]);
        $owner        = Owner::factory()->create(['agency_id' => $this->agency->id]);

        // Percentage using default agency rate (10%)
        $propPercentageDefault = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'rent_amount'      => 200000,
            'agency_fee_type'  => 'percentage',
            'agency_fee_value' => null,
        ]);
        $this->assertEquals(20000.00, $propPercentageDefault->agency_fee_amount);

        // Percentage using custom property rate (15%)
        $propPercentageCustom = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'rent_amount'      => 200000,
            'agency_fee_type'  => 'percentage',
            'agency_fee_value' => 15.00,
        ]);
        $this->assertEquals(30000.00, $propPercentageCustom->agency_fee_amount);

        // Fixed fee (35 000 FCFA)
        $propFixed = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'rent_amount'      => 200000,
            'agency_fee_type'  => 'fixed',
            'agency_fee_value' => 35000.00,
        ]);
        $this->assertEquals(35000.00, $propFixed->agency_fee_amount);
    }

    public function test_net_owner_income_calculation(): void
    {
        $propertyType = PropertyType::factory()->create(['agency_id' => $this->agency->id]);
        $owner        = Owner::factory()->create(['agency_id' => $this->agency->id]);

        // Rent: 250 000 FCFA
        // IRF: 55 500 FCFA
        // Agency Fee (10%): 25 000 FCFA
        // Expected Net: 250 000 - 55 500 - 25 000 = 169 500 FCFA
        $property = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'rent_amount'      => 250000,
            'is_subject_to_irf' => true,
            'agency_fee_type'  => 'percentage',
            'agency_fee_value' => 10.00,
        ]);

        $this->assertEquals(55500.00, $property->irf_amount);
        $this->assertEquals(25000.00, $property->agency_fee_amount);
        $this->assertEquals(169500.00, $property->net_owner_income);
    }

    public function test_create_property_with_irf_and_agency_fee(): void
    {
        $this->actingAs($this->user);

        $owner        = Owner::factory()->create(['agency_id' => $this->agency->id]);
        $propertyType = PropertyType::factory()->create(['agency_id' => $this->agency->id]);

        Livewire::test(\App\Livewire\Properties\Create::class)
            ->set('title', 'Immeuble du Soleil')
            ->set('owner_id', $owner->id)
            ->set('property_type_id', $propertyType->id)
            ->set('city', 'Ouagadougou')
            ->set('address', 'Avenue Kwame N\'Krumah')
            ->set('rent_amount', 300000)
            ->set('is_subject_to_irf', true)
            ->set('agency_fee_type', 'fixed')
            ->set('agency_fee_value', 40000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'title'             => 'Immeuble du Soleil',
            'agency_id'         => $this->agency->id,
            'rent_amount'       => 300000,
            'is_subject_to_irf' => true,
            'agency_fee_type'   => 'fixed',
            'agency_fee_value'  => 40000,
        ]);
    }
}
