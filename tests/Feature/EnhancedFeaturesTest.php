<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Rent\Models\RentHistory;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnhancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $adminUser;
    protected User $tenantUser;
    protected Tenant $tenant;
    protected Property $property;
    protected Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->agency = Agency::create([
            'name'  => 'Agence Immobilière Test',
            'email' => 'contact@agency.com',
        ]);

        $this->adminUser = User::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Gestionnaire',
            'email'     => 'admin@test.com',
            'password'  => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('Administrateur');

        $this->tenantUser = User::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Locataire Test',
            'email'     => 'tenant@test.com',
            'password'  => bcrypt('password'),
        ]);
        $this->tenantUser->assignRole('Locataire');

        $this->tenant = Tenant::create([
            'agency_id'  => $this->agency->id,
            'user_id'    => $this->tenantUser->id,
            'reference'  => 'TEN-0001',
            'first_name' => 'Jean',
            'last_name'  => 'Kouassi',
            'phone'      => '+225070000000',
        ]);

        $owner = Owner::create([
            'agency_id'  => $this->agency->id,
            'reference'  => 'OWN-0001',
            'first_name' => 'Paul',
            'last_name'  => 'Diallo',
        ]);

        $propertyType = PropertyType::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Appartement',
        ]);

        $this->property = Property::create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'reference'        => 'PROP-0001',
            'title'            => 'Appartement F3 Cocody',
            'address'          => 'Rue des Jardins',
            'city'             => 'Abidjan',
            'rent_amount'      => 250000,
            'status'           => 'occupied',
        ]);

        $this->lease = Lease::create([
            'agency_id'       => $this->agency->id,
            'reference'       => 'CON-0001',
            'property_id'     => $this->property->id,
            'tenant_id'       => $this->tenant->id,
            'start_date'      => '2025-01-01',
            'end_date'        => now()->addDays(30)->format('Y-m-d'),
            'rent_amount'     => 250000,
            'payment_due_day' => 5,
            'status'          => 'active',
        ]);
    }

    public function test_agency_receives_notification_when_incident_is_created(): void
    {
        $this->actingAs($this->tenantUser);

        Livewire::test(\App\Livewire\Incidents\Create::class)
            ->set('lease_id', $this->lease->id)
            ->set('title', 'Fuite de canalisation')
            ->set('description', 'Infiltration dans la cuisine')
            ->set('priority', 'urgent')
            ->call('save');

        $this->assertDatabaseHas('notifications', [
            'agency_id' => $this->agency->id,
            'type'      => 'incident_created',
        ]);
    }

    public function test_tenant_receives_notification_when_incident_is_taken_in_charge(): void
    {
        $incident = Incident::create([
            'agency_id'   => $this->agency->id,
            'property_id' => $this->property->id,
            'lease_id'    => $this->lease->id,
            'tenant_id'   => $this->tenant->id,
            'reference'   => 'INC-0001',
            'title'       => 'Serrure défectueuse',
            'description' => 'La porte ne ferme plus',
            'priority'    => 'medium',
            'status'      => 'reported',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\Incidents\Show::class, ['incidentId' => $incident->id])
            ->call('takeInCharge');

        $this->assertDatabaseHas('notifications', [
            'agency_id'      => $this->agency->id,
            'recipient_type' => Tenant::class,
            'recipient_id'   => $this->tenant->id,
            'type'           => 'incident_in_progress',
        ]);
    }

    public function test_can_filter_expiring_leases_and_send_reminders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\Leases\Index::class)
            ->set('statusFilter', 'expiring_soon')
            ->call('notifyTenant', $this->lease->id)
            ->call('notifyAgency', $this->lease->id);

        $this->assertDatabaseHas('notifications', [
            'type' => 'lease_expiration_reminder',
        ]);

        $this->assertDatabaseHas('notifications', [
            'type' => 'lease_expiration_agency_alert',
        ]);
    }

    public function test_can_renew_expiring_lease(): void
    {
        $this->actingAs($this->adminUser);

        $newEndDate = now()->addYear()->format('Y-m-d');

        Livewire::test(\App\Livewire\Leases\Index::class)
            ->call('openRenewModal', $this->lease->id)
            ->set('new_end_date', $newEndDate)
            ->set('new_rent_amount', 270000)
            ->set('renewal_notes', 'Accord de renouvellement annuel')
            ->call('renewLease');

        $this->assertDatabaseHas('leases', [
            'id'          => $this->lease->id,
            'rent_amount' => 270000,
            'status'      => 'active',
        ]);

        $this->assertDatabaseHas('rent_histories', [
            'property_id'     => $this->property->id,
            'old_rent_amount' => 250000,
            'new_rent_amount' => 270000,
            'change_amount'   => 20000,
        ]);
    }

    public function test_can_increase_property_rent_with_reason_and_history(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\Properties\Edit::class, ['propertyId' => $this->property->id])
            ->call('openIncreaseModal')
            ->set('new_rent_amount', 300000)
            ->set('increase_reason', 'Rénovation peinture et climatisation')
            ->set('effective_date', '2026-08-01')
            ->set('update_active_lease', true)
            ->call('increaseRent');

        $this->assertDatabaseHas('properties', [
            'id'          => $this->property->id,
            'rent_amount' => 300000,
        ]);

        $this->assertDatabaseHas('rent_histories', [
            'property_id'     => $this->property->id,
            'old_rent_amount' => 250000,
            'new_rent_amount' => 300000,
            'change_amount'   => 50000,
            'reason'          => 'Rénovation peinture et climatisation',
        ]);
    }
}
