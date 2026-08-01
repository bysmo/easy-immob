<?php

namespace Tests\Unit\Support\Navigation;

use App\Models\User;
use App\Support\Navigation\SidebarMenu;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SidebarMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /**
     * Helper : crée un User sans agence (agency_id null) et lui attribue une liste de permissions directement.
     *
     * @param array<int, string> $permissions
     */
    private function makeUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['agency_id' => null]);
        foreach ($permissions as $perm) {
            $user->givePermissionTo($perm);
        }
        return $user;
    }

    public function test_agency_user_does_not_see_saas_admin_nor_catalog(): void
    {
        // Administrateur complet : a users.view donc voit tout sauf SaaS
        $user = $this->makeUserWithPermissions(
            array_filter(
                Permission::pluck('name')->all(),
                fn (string $p) => $p !== 'saas.admin'
            )
        );
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertNotContains('catalog.index', $routes);
        $this->assertNotContains('admin.saas-dashboard', $routes);
        $this->assertNotContains('admin.agencies.index', $routes);
        $this->assertNotContains('admin.saas-invoices.index', $routes);
        $this->assertNotContains('admin.plans.index', $routes);
    }

    public function test_super_admin_user_only_sees_saas_admin_items(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $user->assignRole('Super Admin');
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertSame(
            ['admin.saas-dashboard', 'admin.agencies.index', 'admin.saas-invoices.index', 'admin.plans.index'],
            $routes
        );
    }

    public function test_tenant_user_sees_catalog(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $user->assignRole('Locataire');
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertContains('catalog.index', $routes);
    }

    public function test_comptable_only_sees_finances_section(): void
    {
        $user = $this->makeUserWithPermissions([
            'rents.view', 'rents.record-payment',
            'deposits.view', 'deposits.manage',
            'arrears.view', 'arrears.manage',
            'documents.view',
            'reports.view',
            'notifications.view',
        ]);
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        // Le Comptable VOIT les finances
        $this->assertContains('rents.index', $routes);
        $this->assertContains('deposits.index', $routes);
        $this->assertContains('arrears.index', $routes);

        // Le Comptable NE VOIT PAS la gestion locative (bailleurs, locataires)
        $this->assertNotContains('owners.index', $routes);
        $this->assertNotContains('tenants.index', $routes);

        // Le Comptable NE VOIT PAS l'administration agence
        $this->assertNotContains('admin.users.index', $routes);
    }

    public function test_gestionnaire_sees_locative_and_reports_but_not_finances(): void
    {
        $user = $this->makeUserWithPermissions([
            'owners.view', 'owners.create', 'owners.update', 'owners.delete', 'owners.import',
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete', 'tenants.import',
            'leases.view', 'leases.create', 'leases.update', 'leases.delete',
            'incidents.view', 'incidents.create', 'incidents.update', 'incidents.manage',
            'notifications.view',
            'documents.view', 'documents.upload',
            'reports.view',
        ]);
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        // Le Gestionnaire VOIT la gestion locative
        $this->assertContains('owners.index', $routes);
        $this->assertContains('tenants.index', $routes);
        $this->assertContains('reports.index', $routes);

        // Le Gestionnaire NE VOIT PAS les finances
        $this->assertNotContains('rents.index', $routes);
        $this->assertNotContains('arrears.index', $routes);

        // Le Gestionnaire NE VOIT PAS l'administration agence
        $this->assertNotContains('admin.users.index', $routes);
    }

    public function test_agent_sees_only_partial_locative(): void
    {
        $user = $this->makeUserWithPermissions([
            'owners.view',
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'tenants.view',
            'incidents.view', 'incidents.create', 'incidents.update',
        ]);
        $this->actingAs($user);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        // L'Agent voit les biens et les bailleurs (lecture)
        $this->assertContains('properties.index', $routes);
        $this->assertContains('owners.index', $routes);

        // L'Agent NE VOIT PAS les finances ni les rapports
        $this->assertNotContains('rents.index', $routes);
        $this->assertNotContains('reports.index', $routes);

        // L'Agent NE VOIT PAS l'administration agence
        $this->assertNotContains('admin.users.index', $routes);
    }
}
