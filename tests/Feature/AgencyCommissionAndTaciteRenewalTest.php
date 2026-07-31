<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AgencyCommissionAndTaciteRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_commission_and_tva_can_be_updated_in_profile(): void
    {
        $agency = Agency::factory()->create([
            'commission_rate'   => 10.00,
            'is_subject_to_tva' => true,
        ]);

        $user = User::factory()->create([
            'agency_id' => $agency->id,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Index::class)
            ->set('agency_commission_rate', 15.5)
            ->set('agency_is_subject_to_tva', false)
            ->call('updateAgencySettings')
            ->assertHasNoErrors()
            ->assertSee('Les paramètres financiers de l\'agence ont été enregistrés avec succès.');

        $this->assertDatabaseHas('agencies', [
            'id'                => $agency->id,
            'commission_rate'   => 15.5,
            'is_subject_to_tva' => false,
        ]);
    }

    public function test_lease_can_be_created_with_tacite_reconduction(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $agency = Agency::factory()->create();
        $user = User::factory()->create(['agency_id' => $agency->id]);
        $user->assignRole('Administrateur');

        $property = \App\Domain\Property\Models\Property::factory()->for($agency, 'agency')->create(['status' => 'available']);
        $tenant   = \App\Domain\Tenant\Models\Tenant::factory()->for($agency, 'agency')->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Leases\Create::class)
            ->set('property_id', $property->id)
            ->set('tenant_id', $tenant->id)
            ->set('start_date', '2026-08-01')
            ->set('end_date', '2027-07-31')
            ->set('is_tacit_renewal', true)
            ->set('rent_amount', 200000)
            ->set('charges_amount', 10000)
            ->set('payment_due_day', 5)
            ->set('deposit_amount', 400000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leases', [
            'property_id'      => $property->id,
            'tenant_id'        => $tenant->id,
            'is_tacit_renewal' => true,
        ]);
    }

    public function test_dashboard_computes_financial_statistics_correctly(): void
    {
        $agency = Agency::factory()->create([
            'commission_rate'   => 10.00,
            'is_subject_to_tva' => true,
        ]);

        $user = User::factory()->create(['agency_id' => $agency->id]);

        $lease1 = Lease::factory()->for($agency, 'agency')->create();
        $lease2 = Lease::factory()->for($agency, 'agency')->create();

        // 1. Rent schedule paid: expected 100 000, paid 100 000
        RentSchedule::create([
            'agency_id'        => $agency->id,
            'lease_id'         => $lease1->id,
            'period'           => '2026-07',
            'due_date'         => '2026-07-05',
            'expected_amount'  => 100000,
            'paid_amount'      => 100000,
            'remaining_amount' => 0,
            'status'           => RentScheduleStatus::Paid,
        ]);

        // 2. Rent schedule overdue: expected 50 000, paid 0
        RentSchedule::create([
            'agency_id'        => $agency->id,
            'lease_id'         => $lease2->id,
            'period'           => '2026-07',
            'due_date'         => '2026-07-05',
            'expected_amount'  => 50000,
            'paid_amount'      => 0,
            'remaining_amount' => 50000,
            'status'           => RentScheduleStatus::Overdue,
        ]);

        // Total expected = 150 000
        // Total paid = 100 000
        // Total unpaid = 50 000
        // Commission (10% of 100 000) = 10 000
        // TVA (18% of 10 000) = 1 800

        Livewire::actingAs($user)
            ->test(\App\Livewire\Dashboard::class)
            ->assertViewHas('totalExpectedRent', 150000.0)
            ->assertViewHas('totalPaidRent', 100000.0)
            ->assertViewHas('totalUnpaidRent', 50000.0)
            ->assertViewHas('totalCommission', 10000.0)
            ->assertViewHas('totalTva', 1800.0);
    }
}
