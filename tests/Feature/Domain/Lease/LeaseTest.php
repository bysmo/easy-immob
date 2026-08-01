<?php

namespace Tests\Feature\Domain\Lease;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Actions\ActivateLeaseAction;
use App\Domain\Lease\Actions\TerminateLeaseAction;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Leases\Create;
use App\Livewire\Leases\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_can_view_leases_list(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        Lease::factory()->for($agency, 'agency')->create(['reference' => 'CON-0001']);

        Livewire::actingAs($user)->test(Index::class)
            ->assertSee('CON-0001');
    }

    public function test_user_can_create_lease_as_draft(): void
    {
        $agency   = Agency::factory()->create();
        $user     = $this->createAuthorizedUser($agency);
        $property = Property::factory()->for($agency, 'agency')->create(['rent_amount' => 150000]);
        $tenant   = Tenant::factory()->for($agency, 'agency')->create();
        $template = LeaseTemplate::factory()->for($agency, 'agency')->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('property_id', $property->id)
            ->set('tenant_id', $tenant->id)
            ->set('template_id', $template->id)
            ->set('start_date', '2026-08-01')
            ->set('end_date', '2027-07-31')
            ->set('rent_amount', 150000)
            ->set('charges_amount', 10000)
            ->set('payment_due_day', 5)
            ->set('deposit_amount', 300000)
            ->call('save')
            ->assertRedirect(route('leases.show', 1));

        $this->assertDatabaseHas('leases', [
            'agency_id'   => $agency->id,
            'reference'   => 'CON-0001',
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'status'      => LeaseStatus::Draft->value,
        ]);
    }

    public function test_activating_lease_changes_property_status_to_occupied_and_generates_schedules(): void
    {
        $agency   = Agency::factory()->create();
        $property = Property::factory()->for($agency, 'agency')->create(['status' => PropertyStatus::Available]);
        $lease    = Lease::factory()->for($agency, 'agency')->create([
            'property_id'     => $property->id,
            'start_date'      => '2026-08-01',
            'end_date'        => '2027-07-31',
            'rent_amount'     => 100000,
            'charges_amount'  => 10000,
            'payment_due_day' => 5,
            'status'          => LeaseStatus::Draft,
        ]);

        /** @var ActivateLeaseAction $action */
        $action = app(ActivateLeaseAction::class);
        $action->execute($lease);

        $this->assertEquals(LeaseStatus::Active, $lease->fresh()->status);
        $this->assertEquals(PropertyStatus::Occupied, $property->fresh()->status);
        $this->assertCount(12, $lease->fresh()->rentSchedules);
    }

    public function test_activating_overlapping_lease_fails(): void
    {
        $agency   = Agency::factory()->create();
        $property = Property::factory()->for($agency, 'agency')->create(['status' => PropertyStatus::Available]);

        // Premier contrat actif
        Lease::factory()->for($agency, 'agency')->active()->create([
            'property_id' => $property->id,
            'start_date'  => '2026-08-01',
            'end_date'    => '2027-07-31',
        ]);

        // Deuxième contrat chevauchant
        $overlappingLease = Lease::factory()->for($agency, 'agency')->create([
            'property_id' => $property->id,
            'start_date'  => '2026-10-01',
            'end_date'    => '2027-09-30',
            'status'      => LeaseStatus::Draft,
        ]);

        /** @var ActivateLeaseAction $action */
        $action = app(ActivateLeaseAction::class);

        $this->expectException(\InvalidArgumentException::class);
        $action->execute($overlappingLease);
    }

    public function test_terminating_lease_changes_property_status_back_to_available(): void
    {
        $agency   = Agency::factory()->create();
        $property = Property::factory()->for($agency, 'agency')->create(['status' => PropertyStatus::Occupied]);
        $lease    = Lease::factory()->for($agency, 'agency')->active()->create([
            'property_id' => $property->id,
        ]);

        /** @var TerminateLeaseAction $action */
        $action = app(TerminateLeaseAction::class);
        $action->execute($lease);

        $this->assertEquals(LeaseStatus::Terminated, $lease->fresh()->status);
        $this->assertEquals(PropertyStatus::Available, $property->fresh()->status);
    }

    public function test_lease_templates_index_page_can_be_rendered(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        $response = $this->actingAs($user)->get(route('admin.lease-templates.index'));
        $response->assertOk();
    }

    public function test_template_variable_replacer_replaces_all_party_and_manager_variables(): void
    {
        $agency = Agency::factory()->create([
            'name'            => 'KIPRESS ESTATE SARL',
            'manager_name'    => 'CONGO ERIC AMED WENDKUNI',
            'manager_title'   => 'Gérant',
            'manager_phone'   => '+226 25 65 92 12',
            'manager_id_card' => 'CNIB N°B15795168',
        ]);

        $tenant = Tenant::factory()->for($agency, 'agency')->create([
            'first_name'     => 'MARIAM',
            'last_name'      => 'COMPAORE',
            'profession'     => 'Secrétaire de Direction',
            'nationality'    => 'Burkinabè',
            'id_card_number' => 'CNIB N°B18203984',
        ]);

        $lease = Lease::factory()->for($agency, 'agency')->create([
            'tenant_id' => $tenant->id,
        ]);

        $replacer = app(\App\Domain\Lease\Services\TemplateVariableReplacer::class);
        $result = $replacer->replaceForLease(
            'Locataire: {locataire_nom_complet}, Pro: {locataire_profession}, Id: {locataire_piece_identite}, Agence: {agence_nom}, Gerant: {agence_gerant}',
            $lease
        );

        $this->assertStringContainsString('COMPAORE MARIAM', $result);
        $this->assertStringContainsString('Secrétaire de Direction', $result);
        $this->assertStringContainsString('CNIB N°B18203984', $result);
        $this->assertStringContainsString('KIPRESS ESTATE SARL', $result);
        $this->assertStringContainsString('CONGO ERIC AMED WENDKUNI', $result);
    }

    private function createAuthorizedUser(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Administrateur');

        return $user;
    }
}
