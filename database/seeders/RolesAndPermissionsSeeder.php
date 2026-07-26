<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * All permissions in the system, grouped by domain (docs/03-ROLES-ET-PERMISSIONS.md).
     *
     * @var array<int, string>
     */
    private const PERMISSIONS = [
        'users.view', 'users.create', 'users.update', 'users.delete', 'users.manage-roles',
        'owners.view', 'owners.create', 'owners.update', 'owners.delete',
        'properties.view', 'properties.create', 'properties.update', 'properties.delete',
        'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete',
        'leases.view', 'leases.create', 'leases.update', 'leases.delete',
        'rents.view', 'rents.record-payment',
        'deposits.view', 'deposits.manage',
        'arrears.view', 'arrears.manage',
        'notifications.view',
        'documents.view', 'documents.upload',
        'reports.view',
        'audit.view',
    ];

    /**
     * Permissions granted per role, beyond Administrateur (which gets everything).
     *
     * @var array<string, array<int, string>>
     */
    private const ROLE_PERMISSIONS = [
        'Gestionnaire' => [
            'owners.view', 'owners.create', 'owners.update', 'owners.delete',
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete',
            'leases.view', 'leases.create', 'leases.update', 'leases.delete',
            'rents.view', 'rents.record-payment',
            'arrears.view', 'arrears.manage',
        ],
        'Comptable' => [
            'rents.view', 'rents.record-payment',
            'deposits.view', 'deposits.manage',
            'documents.view',
            'reports.view',
        ],
        'Agent' => [
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'owners.view',
        ],
        'Propriétaire' => [],
        'Locataire' => [],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission);
        }

        $administrateur = Role::findOrCreate('Administrateur');
        $administrateur->syncPermissions(self::PERMISSIONS);

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            Role::findOrCreate($roleName)->syncPermissions($permissions);
        }
    }
}
