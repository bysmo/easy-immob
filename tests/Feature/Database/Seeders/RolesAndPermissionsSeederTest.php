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

    public function test_it_creates_the_six_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(
            ['Administrateur', 'Gestionnaire', 'Comptable', 'Agent', 'Propriétaire', 'Locataire'],
            Role::orderBy('id')->pluck('name')->all(),
        );
    }

    public function test_administrateur_has_every_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::findByName('Administrateur');

        $this->assertSame(Permission::count(), $admin->permissions()->count());
    }

    public function test_gestionnaire_cannot_manage_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $gestionnaire = Role::findByName('Gestionnaire');

        $this->assertFalse($gestionnaire->hasPermissionTo('users.manage-roles'));
        $this->assertTrue($gestionnaire->hasPermissionTo('properties.view'));
        $this->assertTrue($gestionnaire->hasPermissionTo('arrears.manage'));
    }

    public function test_proprietaire_and_locataire_have_no_internal_permissions_yet(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(0, Role::findByName('Propriétaire')->permissions()->count());
        $this->assertSame(0, Role::findByName('Locataire')->permissions()->count());
    }
}
