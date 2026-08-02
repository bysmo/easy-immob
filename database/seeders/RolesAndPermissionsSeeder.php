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
        // Utilisateurs
        'users.view', 'users.create', 'users.update', 'users.delete', 'users.manage-roles',
        // Bailleurs
        'owners.view', 'owners.create', 'owners.update', 'owners.delete', 'owners.import',
        // Biens
        'properties.view', 'properties.create', 'properties.update', 'properties.delete',
        // Locataires
        'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete', 'tenants.import',
        // Baux
        'leases.view', 'leases.create', 'leases.update', 'leases.delete',
        // Finances
        'rents.view', 'rents.record-payment',
        'deposits.view', 'deposits.manage',
        'arrears.view', 'arrears.manage',
        // Incidents
        'incidents.view', 'incidents.create', 'incidents.update', 'incidents.manage',
        // Divers
        'notifications.view',
        'documents.view', 'documents.upload',
        'reports.view',
        'audit.view',
        // Portail Bailleur (espace propriétaire)
        'owner.portal.view',
        'owner.portal.confirm_repair',
        'owner.portal.revoke_contract',
        // SaaS réservé
        'saas.admin',
    ];

    /**
     * Permissions par rôle.
     *
     * Modules :
     *   - Gestion Locative  : owners, properties, tenants, leases, incidents
     *   - Finances          : rents, deposits, arrears
     *   - Suivi & Rapports  : notifications, documents, reports
     *   - Administration    : users (Administrateur uniquement)
     *
     * @var array<string, array<int, string>>
     */
    private const ROLE_PERMISSIONS = [

        // -------------------------------------------------------------------
        // Gestionnaire : Gestion Locative complète + Suivi/Rapports
        // -------------------------------------------------------------------
        'Gestionnaire' => [
            // Gestion Locative
            'owners.view', 'owners.create', 'owners.update', 'owners.delete', 'owners.import',
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete', 'tenants.import',
            'leases.view', 'leases.create', 'leases.update', 'leases.delete',
            'incidents.view', 'incidents.create', 'incidents.update', 'incidents.manage',
            // Suivi & Rapports
            'notifications.view',
            'documents.view', 'documents.upload',
            'reports.view',
        ],

        // -------------------------------------------------------------------
        // Comptable : Uniquement Finances & Recouvrement + Rapports lecture
        // -------------------------------------------------------------------
        'Comptable' => [
            // Finances
            'rents.view', 'rents.record-payment',
            'deposits.view', 'deposits.manage',
            'arrears.view', 'arrears.manage',
            // Rapports (lecture seule)
            'documents.view',
            'reports.view',
            'notifications.view',
        ],

        // -------------------------------------------------------------------
        // Agent : Gestion Locative partielle (pas les finances, pas les rapports)
        // -------------------------------------------------------------------
        'Agent' => [
            // Lecture bailleurs
            'owners.view',
            // Biens complets
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            // Lecture locataires
            'tenants.view',
            // Incidents limités
            'incidents.view', 'incidents.create', 'incidents.update',
        ],

        // -------------------------------------------------------------------
        // Portail Bailleur : accès à son espace propriétaire uniquement
        // -------------------------------------------------------------------
        'Bailleur' => [
            'owner.portal.view',
            'owner.portal.confirm_repair',
            'owner.portal.revoke_contract',
        ],

        'Locataire' => [
            'incidents.view', 'incidents.create',
            'rents.view',
            'leases.view',
        ],
    ];

    public function run(): void
    {
        // Créer toutes les permissions
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission);
        }

        // Super Admin SaaS : Accès complet à la plateforme
        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->syncPermissions(self::PERMISSIONS);

        // Administrateur Agence : Tout sauf saas.admin
        $agencyPermissions = array_filter(self::PERMISSIONS, fn ($p) => $p !== 'saas.admin');
        $administrateur    = Role::findOrCreate('Administrateur');
        $administrateur->syncPermissions($agencyPermissions);

        // Rôles spécialisés
        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            Role::findOrCreate($roleName)->syncPermissions($permissions);
        }
    }
}
