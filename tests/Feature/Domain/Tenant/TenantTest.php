<?php

namespace Tests\Feature\Domain\Tenant;

use App\Domain\Agency\Models\Agency;
use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Tenants\Create;
use App\Livewire\Tenants\Edit;
use App\Livewire\Tenants\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_can_view_tenants_list(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        Tenant::factory()->for($agency, 'agency')->create(['first_name' => 'Paul', 'last_name' => 'Kone']);

        Livewire::actingAs($user)->test(Index::class)
            ->assertSee('Kone Paul');
    }

    public function test_user_can_create_tenant_with_reference(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('first_name', 'Ali')
            ->set('last_name', 'Traore')
            ->set('email', 'ali@example.com')
            ->set('phone', '0504030201')
            ->call('save')
            ->assertRedirect(route('tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'agency_id'  => $agency->id,
            'reference'  => 'LOC-0001',
            'first_name' => 'Ali',
            'last_name'  => 'Traore',
        ]);
    }

    public function test_user_can_edit_tenant(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);
        $tenant = Tenant::factory()->for($agency, 'agency')->create(['first_name' => 'Ancien']);

        Livewire::actingAs($user)
            ->test(Edit::class, ['tenantId' => $tenant->id])
            ->set('first_name', 'Nouveau')
            ->call('save')
            ->assertRedirect(route('tenants.index'));

        $this->assertEquals('Nouveau', $tenant->fresh()->first_name);
    }

    public function test_user_can_soft_delete_tenant(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);
        $tenant = Tenant::factory()->for($agency, 'agency')->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $tenant->id);

        $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
    }

    public function test_tenant_is_scoped_to_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $userA   = $this->createAuthorizedUser($agencyA);
        $tenantB = Tenant::factory()->for($agencyB, 'agency')->create(['first_name' => 'TenantB']);

        Livewire::actingAs($userA)->test(Index::class)
            ->assertDontSee('TenantB');
    }

    private function createAuthorizedUser(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Administrateur');

        return $user;
    }
}
