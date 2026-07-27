<?php

namespace App\Support\Navigation;

class SidebarMenu
{
    /**
     * @return array<int, array{label: string, icon: string, route: string, params: array<string, mixed>}>
     */
    public static function items(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => '📊', 'route' => 'dashboard', 'params' => []],
            ['label' => 'Propriétaires', 'icon' => '👤', 'route' => 'owners.index', 'params' => []],
            ['label' => 'Biens', 'icon' => '🏠', 'route' => 'properties.index', 'params' => []],
            ['label' => 'Locataires', 'icon' => '🧑', 'route' => 'tenants.index', 'params' => []],
            ['label' => 'Contrats', 'icon' => '📄', 'route' => 'leases.index', 'params' => []],
            ['label' => 'Loyers', 'icon' => '💰', 'route' => 'rents.index', 'params' => []],
            ['label' => 'Cautions', 'icon' => '🔒', 'route' => 'deposits.index', 'params' => []],
            ['label' => 'Impayés', 'icon' => '⚠️', 'route' => 'arrears.index', 'params' => []],
            ['label' => 'Notifications', 'icon' => '🔔', 'route' => 'notifications.index', 'params' => []],
            ['label' => 'Rapports', 'icon' => '📈', 'route' => 'reports.index', 'params' => []],
            ['label' => 'Modèles de contrat', 'icon' => '📑', 'route' => 'admin.lease-templates.index', 'params' => []],
            ['label' => 'Types de biens', 'icon' => '🏷️', 'route' => 'admin.property-types.index', 'params' => []],
            ['label' => 'Administration', 'icon' => '⚙️', 'route' => 'admin.users.index', 'params' => []],
        ];
    }
}
