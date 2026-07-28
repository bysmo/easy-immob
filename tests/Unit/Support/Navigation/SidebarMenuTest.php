<?php

namespace Tests\Unit\Support\Navigation;

use App\Models\User;
use App\Support\Navigation\SidebarMenu;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SidebarMenuTest extends TestCase
{
    public function test_agency_user_does_not_see_saas_admin_nor_catalog(): void
    {
        $this->actingAs(new User());

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertNotContains('catalog.index', $routes);
        $this->assertNotContains('admin.saas-dashboard', $routes);
        $this->assertNotContains('admin.agencies.index', $routes);
        $this->assertNotContains('admin.saas-invoices.index', $routes);
        $this->assertNotContains('admin.plans.index', $routes);
    }

    public function test_super_admin_user_only_sees_saas_admin_items(): void
    {
        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminUser = new User();
        $superAdminUser->setRelation('roles', collect([$superAdminRole]));
        $this->actingAs($superAdminUser);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertSame(
            ['admin.saas-dashboard', 'admin.agencies.index', 'admin.saas-invoices.index', 'admin.plans.index'],
            $routes
        );
    }

    public function test_tenant_user_sees_catalog(): void
    {
        $tenantRole = Role::findOrCreate('Locataire');
        $tenantUser = new User();
        $tenantUser->setRelation('roles', collect([$tenantRole]));
        $this->actingAs($tenantUser);

        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $this->assertContains('catalog.index', $routes);
    }
}
