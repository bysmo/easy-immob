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
            ['label' => 'Propriétaires', 'icon' => '👤', 'route' => 'modules.coming-soon', 'params' => ['module' => 'proprietaires']],
            ['label' => 'Biens', 'icon' => '🏠', 'route' => 'modules.coming-soon', 'params' => ['module' => 'biens']],
            ['label' => 'Locataires', 'icon' => '🧑', 'route' => 'modules.coming-soon', 'params' => ['module' => 'locataires']],
            ['label' => 'Contrats', 'icon' => '📄', 'route' => 'modules.coming-soon', 'params' => ['module' => 'contrats']],
            ['label' => 'Loyers', 'icon' => '💰', 'route' => 'modules.coming-soon', 'params' => ['module' => 'loyers']],
            ['label' => 'Cautions', 'icon' => '🔒', 'route' => 'modules.coming-soon', 'params' => ['module' => 'cautions']],
            ['label' => 'Impayés', 'icon' => '⚠️', 'route' => 'modules.coming-soon', 'params' => ['module' => 'impayes']],
            ['label' => 'Notifications', 'icon' => '🔔', 'route' => 'modules.coming-soon', 'params' => ['module' => 'notifications']],
            ['label' => 'Rapports', 'icon' => '📈', 'route' => 'modules.coming-soon', 'params' => ['module' => 'rapports']],
            ['label' => 'Administration', 'icon' => '⚙️', 'route' => 'admin.users.index', 'params' => []],
        ];
    }
}
