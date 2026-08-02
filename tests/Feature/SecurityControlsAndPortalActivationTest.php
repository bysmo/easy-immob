<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\ManagementContracts\Index as ContractsIndex;
use App\Livewire\Owners\Edit as OwnerEdit;
use App\Livewire\Owners\Index as OwnerIndex;
use App\Livewire\Properties\Edit as PropertyEdit;
use App\Livewire\Properties\Index as PropertyIndex;
use App\Livewire\TenantPortal\Activate as TenantActivate;
use App\Livewire\Tenants\Create as TenantCreate;
use App\Livewire\Tenants\Edit as TenantEdit;
use App\Livewire\Tenants\Index as TenantIndex;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityControlsAndPortalActivationTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $agent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->agency = Agency::factory()->create();

        $this->agent = User::factory()->create([
            'agency_id' => $this->agency->id,
        ]);
        $this->agent->assignRole('Administrateur');
    }

    public function test_assigned_property_cannot_be_deleted_or_deactivated(): void
    {
        $owner = Owner::factory()->create(['agency_id' => $this->agency->id]);
        $type  = PropertyType::factory()->create();

        $property = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $type->id,
            'status'           => PropertyStatus::Occupied,
        ]);

        $tenant = Tenant::factory()->create(['agency_id' => $this->agency->id]);

        Lease::factory()->create([
            'agency_id'   => $this->agency->id,
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'status'      => LeaseStatus::Active,
        ]);

        $this->assertTrue($property->isAssignedToTenant());

        // Test deletion block
        Livewire::actingAs($this->agent)
            ->test(PropertyIndex::class)
            ->call('delete', $property->id);

        $this->assertDatabaseHas('properties', ['id' => $property->id]);

        // Test deactivation block
        Livewire::actingAs($this->agent)
            ->test(PropertyEdit::class, ['propertyId' => $property->id])
            ->set('status', PropertyStatus::Inactive->value)
            ->call('save')
            ->assertHasErrors(['status']);
    }

    public function test_owner_with_active_portal_cannot_be_deleted_or_updated(): void
    {
        $portalUser = User::factory()->create([
            'agency_id'         => $this->agency->id,
            'email_verified_at' => now(),
        ]);
        $portalUser->assignRole('Bailleur');

        $owner = Owner::factory()->create([
            'agency_id' => $this->agency->id,
            'user_id'   => $portalUser->id,
            'status'    => 'active',
        ]);

        $this->assertTrue($owner->isPortalActive());

        // Deletion block
        Livewire::actingAs($this->agent)
            ->test(OwnerIndex::class)
            ->call('delete', $owner->id);

        $this->assertDatabaseHas('owners', ['id' => $owner->id]);

        // Update block
        Livewire::actingAs($this->agent)
            ->test(OwnerEdit::class, ['ownerId' => $owner->id])
            ->set('first_name', 'ModifRefusee')
            ->call('save');

        $this->assertDatabaseHas('owners', [
            'id'         => $owner->id,
            'first_name' => $owner->first_name,
        ]);
    }

    public function test_active_management_contract_cannot_be_deleted(): void
    {
        $owner = Owner::factory()->create(['agency_id' => $this->agency->id]);

        $contract = ManagementContract::create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'reference'        => 'MDT-TEST-001',
            'title'            => 'Mandat de gestion Test',
            'start_date'       => now(),
            'end_date'         => now()->addYear(),
            'commission_type'  => 'percentage',
            'commission_value' => 10,
            'status'           => ManagementContractStatus::Active,
        ]);

        Livewire::actingAs($this->agent)
            ->test(ContractsIndex::class)
            ->call('delete', $contract->id);

        $this->assertDatabaseHas('management_contracts', ['id' => $contract->id]);
    }

    public function test_tenant_creation_prevents_duplicates_and_sets_up_portal_activation(): void
    {
        // 1. Create initial tenant
        Livewire::actingAs($this->agent)
            ->test(TenantCreate::class)
            ->set('first_name', 'Mamadou')
            ->set('last_name', 'Ouedraogo')
            ->set('email', 'mamadou@example.com')
            ->set('id_card_number', 'B12345678')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $tenant = Tenant::withoutGlobalScopes()->where('email', 'mamadou@example.com')->first();
        $this->assertNotNull($tenant);
        $this->assertNotNull($tenant->user_id);
        $this->assertFalse($tenant->isPortalActive()); // Email not verified yet

        // 2. Duplicate email attempt in same agency
        Livewire::actingAs($this->agent)
            ->test(TenantCreate::class)
            ->set('first_name', 'Autre')
            ->set('last_name', 'Personne')
            ->set('email', 'mamadou@example.com')
            ->call('save')
            ->assertHasErrors(['email']);

        // 3. Activation via Signed URL
        $signedUrl = URL::temporarySignedRoute(
            'tenant-portal.activate',
            now()->addHours(72),
            ['user' => $tenant->user_id],
        );

        $tenantUser = User::find($tenant->user_id);

        Livewire::test(TenantActivate::class, ['user' => $tenantUser])
            ->set('password', 'NewSecret123')
            ->set('password_confirmation', 'NewSecret123')
            ->call('activate')
            ->assertHasNoErrors();

        $tenant->refresh()->load('user');
        $this->assertTrue($tenant->isPortalActive());

        Livewire::actingAs($this->agent)
            ->test(TenantEdit::class, ['tenantId' => $tenant->id])
            ->set('first_name', 'Pirate')
            ->call('save');

        $this->assertDatabaseHas('tenants', [
            'id'         => $tenant->id,
            'first_name' => 'Mamadou',
        ]);
    }

    public function test_forgot_password_executes_safely(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'unknown@example.com')
            ->call('sendLink')
            ->assertSet('sent', true);
    }
}
