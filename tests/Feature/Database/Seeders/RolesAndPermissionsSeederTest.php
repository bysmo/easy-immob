<?php

namespace Tests\Feature\Database\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_expected_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(
            ['Super Admin', 'Administrateur', 'Gestionnaire', 'Comptable', 'Agent', 'Bailleur', 'Locataire'],
            Role::orderBy('id')->pluck('name')->all(),
        );
    }

    public function test_super_admin_has_every_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = Role::findByName('Super Admin');

        $this->assertSame(Permission::count(), $superAdmin->permissions()->count());
    }

    public function test_administrateur_has_agency_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::findByName('Administrateur');

        $this->assertTrue($admin->hasPermissionTo('users.view'));
        $this->assertTrue($admin->hasPermissionTo('properties.view'));
        $this->assertFalse($admin->hasPermissionTo('saas.admin'));
    }

    public function test_gestionnaire_cannot_manage_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $gestionnaire = Role::findByName('Gestionnaire');

        // Le Gestionnaire n'a PAS accès aux finances ni à la gestion des utilisateurs
        $this->assertFalse($gestionnaire->hasPermissionTo('users.manage-roles'));
        $this->assertFalse($gestionnaire->hasPermissionTo('rents.view'));
        $this->assertFalse($gestionnaire->hasPermissionTo('arrears.manage'));
        // Mais il a accès à la gestion locative et aux rapports
        $this->assertTrue($gestionnaire->hasPermissionTo('properties.view'));
        $this->assertTrue($gestionnaire->hasPermissionTo('tenants.create'));
        $this->assertTrue($gestionnaire->hasPermissionTo('reports.view'));
    }

    public function test_role_permissions_assignment(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertTrue(Role::findByName('Bailleur')->hasPermissionTo('owner.portal.view'));
        $this->assertTrue(Role::findByName('Locataire')->hasPermissionTo('incidents.create'));
        $this->assertTrue(Role::findByName('Locataire')->hasPermissionTo('rents.view'));
    }
}
