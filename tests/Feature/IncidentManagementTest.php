<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class IncidentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $agencyAdmin;
    protected User $tenantUser;
    protected Owner $owner;
    protected PropertyType $propertyType;
    protected Tenant $tenant;
    protected Property $property;
    protected Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        Storage::fake('public');

        $this->agency = Agency::create(['name' => 'Agence Test', 'email' => 'agence@test.com']);
        
        $this->agencyAdmin = User::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Admin Agence',
            'email'     => 'admin@agence.com',
            'password'  => bcrypt('password'),
        ]);
        $this->agencyAdmin->assignRole('Administrateur');

        $this->tenantUser = User::create([
            'agency_id' => null,
            'name'      => 'Awa Sanogo',
            'email'     => 'awa@sanogo.com',
            'password'  => bcrypt('password'),
        ]);
        $this->tenantUser->assignRole('Locataire');

        $this->owner = Owner::create([
            'agency_id'  => $this->agency->id,
            'reference'  => 'PRO-0001',
            'first_name' => 'Ibrahim',
            'last_name'  => 'Koné',
            'email'      => 'owner@test.com',
        ]);

        $this->propertyType = PropertyType::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Appartement',
        ]);

        $this->tenant = Tenant::create([
            'agency_id'  => $this->agency->id,
            'user_id'    => $this->tenantUser->id,
            'reference'  => 'LOC-123456',
            'first_name' => 'Awa',
            'last_name'  => 'Sanogo',
            'email'      => 'awa@sanogo.com',
        ]);

        $this->property = Property::create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $this->owner->id,
            'property_type_id' => $this->propertyType->id,
            'reference'        => 'BIE-0001',
            'title'            => 'Appartement 3 Pièces',
            'address'          => 'Rue 12',
            'city'             => 'Abidjan',
            'rent_amount'      => 300000,
            'status'           => 'occupied',
        ]);

        $this->lease = Lease::create([
            'agency_id'       => $this->agency->id,
            'reference'       => 'CON-0001',
            'property_id'     => $this->property->id,
            'tenant_id'       => $this->tenant->id,
            'start_date'      => '2026-01-01',
            'end_date'        => '2026-12-31',
            'rent_amount'     => 300000,
            'payment_due_day' => 5,
            'status'          => 'active',
        ]);
    }

    public function test_tenant_can_report_an_incident_with_audio_and_photos(): void
    {
        $this->actingAs($this->tenantUser);

        Livewire::test(\App\Livewire\Incidents\Create::class)
            ->set('lease_id', $this->lease->id)
            ->set('title', 'Dégât des eaux sous évier')
            ->set('description', 'Le tuyau fuit abondamment.')
            ->set('priority', 'high')
            ->call('save');

        $incident = Incident::where('title', 'Dégât des eaux sous évier')->first();
        $this->assertNotNull($incident);
        $this->assertEquals('reported', $incident->status->value);
        $this->assertEquals($this->tenant->id, $incident->tenant_id);
    }

    public function test_agency_can_process_incident_and_set_repair_cost(): void
    {
        $incident = Incident::create([
            'agency_id'   => $this->agency->id,
            'property_id' => $this->property->id,
            'lease_id'    => $this->lease->id,
            'tenant_id'   => $this->tenant->id,
            'reference'   => 'INC-0001',
            'title'       => 'Prise électrique défectueuse',
            'description' => 'Courts-circuits fréquents.',
            'status'      => 'reported',
        ]);

        $this->actingAs($this->agencyAdmin);

        Livewire::test(\App\Livewire\Incidents\Show::class, ['incidentId' => $incident->id])
            ->call('takeInCharge')
            ->set('repair_details', 'Remplacement du disjoncteur différentiel.')
            ->set('repair_cost', 45000)
            ->call('resolve');

        $incident->refresh();
        $this->assertEquals('resolved', $incident->status->value);
        $this->assertEquals(45000, (float)$incident->repair_cost);
        $this->assertEquals(45000, (float)$this->property->fresh()->total_maintenance_cost);
    }

    public function test_tenant_can_confirm_repair_with_photo_to_close_incident(): void
    {
        $incident = Incident::create([
            'agency_id'      => $this->agency->id,
            'property_id'    => $this->property->id,
            'lease_id'       => $this->lease->id,
            'tenant_id'      => $this->tenant->id,
            'reference'      => 'INC-0002',
            'title'          => 'Robinet cassé',
            'description'    => 'Robinet de cuisine cassé.',
            'status'         => 'resolved',
            'repair_details' => 'Nouveau robinet installé par le plombier.',
            'repair_cost'    => 20000,
        ]);

        $this->actingAs($this->tenantUser);

        $confirmPhoto = UploadedFile::fake()->image('confirmation.jpg');

        Livewire::test(\App\Livewire\Incidents\Show::class, ['incidentId' => $incident->id])
            ->set('confirmation_photo', $confirmPhoto)
            ->set('tenant_confirmation_note', 'Robinet neuf et fonctionnel, merci !')
            ->call('confirmResolution');

        $incident->refresh();
        $this->assertEquals('closed', $incident->status->value);
        $this->assertNotNull($incident->tenant_confirmation_photo);
        $this->assertEquals('Robinet neuf et fonctionnel, merci !', $incident->tenant_confirmation_note);
    }

    public function test_tenant_can_view_property_info_and_upload_recorded_audio(): void
    {
        $this->actingAs($this->tenantUser);

        $audioFile = UploadedFile::fake()->create('note_vocale.webm', 500, 'video/webm');

        $component = Livewire::test(\App\Livewire\Incidents\Create::class)
            ->assertSee('Appartement 3 Pièces')
            ->set('lease_id', $this->lease->id)
            ->set('title', 'Serrure bloquée')
            ->set('description', 'La porte d entrée ne s ouvre plus avec la clé.')
            ->set('priority', 'urgent')
            ->set('audio', $audioFile)
            ->call('save');

        $component->assertHasNoErrors();

        $incident = Incident::where('title', 'Serrure bloquée')->first();
        $this->assertNotNull($incident);
        $this->assertNotNull($incident->audio_path);
        Storage::disk('public')->assertExists($incident->audio_path);
    }
}
