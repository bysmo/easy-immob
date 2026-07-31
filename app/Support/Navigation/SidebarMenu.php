<?php

namespace App\Support\Navigation;

use Illuminate\Support\Facades\Auth;

class SidebarMenu
{
    /**
     * @return array<int, array{
     *     section: string,
     *     items: array<int, array{label: string, icon: string, route: string, params: array<string, mixed>}>
     * }>
     */
    public static function groupedItems(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::hasUser() ? Auth::user() : null;

        // 1. Le Super Admin SaaS voit EXCLUSIVEMENT les outils d'administration SaaS
        if ($user && $user->isSuperAdmin()) {
            return [
                [
                    'section' => 'Espace Admin SaaS',
                    'items' => [
                        ['label' => 'Dashboard SaaS', 'icon' => 'dashboard', 'route' => 'admin.saas-dashboard', 'params' => []],
                        ['label' => 'Agences Clients', 'icon' => 'owners', 'route' => 'admin.agencies.index', 'params' => []],
                        ['label' => 'Factures SaaS Agences', 'icon' => 'rents', 'route' => 'admin.saas-invoices.index', 'params' => []],
                        ['label' => 'Forfaits & Offres SaaS', 'icon' => 'building', 'route' => 'admin.plans.index', 'params' => []],
                    ],
                ],
            ];
        }

        // 2. Le Locataire voit son espace dédié
        if ($user && $user->isTenant()) {
            return [
                [
                    'section' => 'Mon Espace Locataire',
                    'items' => [
                        ['label' => 'Tableau de bord', 'icon' => 'dashboard', 'route' => 'dashboard', 'params' => []],
                        ['label' => 'Rechercher un bien', 'icon' => 'building', 'route' => 'catalog.index', 'params' => []],
                        ['label' => 'Messagerie & Échanges', 'icon' => 'notifications', 'route' => 'inquiries.index', 'params' => []],
                    ],
                ],
                [
                    'section' => 'Demandes & Intervention',
                    'items' => [
                        ['label' => 'Incidents & Réparations', 'icon' => 'bell', 'route' => 'incidents.index', 'params' => []],
                        ['label' => 'Notifications', 'icon' => 'notifications', 'route' => 'notifications.index', 'params' => []],
                    ],
                ],
            ];
        }

        // 3. L'Agence Immobilière voit la gestion locative
        return [
            [
                'section' => 'Pilotage',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'params' => []],
                    ['label' => 'Messagerie Locataires', 'icon' => 'notifications', 'route' => 'inquiries.index', 'params' => []],
                ],
            ],
            [
                'section' => 'Gestion Locative',
                'items' => [
                    ['label' => 'Bailleurs', 'icon' => 'owners', 'route' => 'owners.index', 'params' => []],
                    ['label' => 'Biens Immobiliers', 'icon' => 'properties', 'route' => 'properties.index', 'params' => []],
                    ['label' => 'Locataires', 'icon' => 'tenants', 'route' => 'tenants.index', 'params' => []],
                    ['label' => 'Contrats de Bail', 'icon' => 'leases', 'route' => 'leases.index', 'params' => []],
                    ['label' => 'Incidents & Réparations', 'icon' => 'bell', 'route' => 'incidents.index', 'params' => []],
                ],
            ],
            [
                'section' => 'Finances & Recouvrement',
                'items' => [
                    ['label' => 'Loyers & Échéances', 'icon' => 'rents', 'route' => 'rents.index', 'params' => []],
                    ['label' => 'Reversements Bailleurs', 'icon' => 'owners', 'route' => 'owners.payouts.index', 'params' => []],
                    ['label' => 'Cautions & Dépôts', 'icon' => 'deposits', 'route' => 'deposits.index', 'params' => []],
                    ['label' => 'Gestion des Impayés', 'icon' => 'arrears', 'route' => 'arrears.index', 'params' => []],
                ],
            ],
            [
                'section' => 'Suivi & Rapports',
                'items' => [
                    ['label' => 'Notifications', 'icon' => 'notifications', 'route' => 'notifications.index', 'params' => []],
                    ['label' => 'Rapports', 'icon' => 'reports', 'route' => 'reports.index', 'params' => []],
                ],
            ],
            [
                'section' => 'Administration Agence',
                'items' => [
                    ['label' => 'Informations Agence', 'icon' => 'building', 'route' => 'agency.settings', 'params' => []],
                    ['label' => 'Mon Abonnement', 'icon' => 'rents', 'route' => 'subscription.index', 'params' => []],
                    ['label' => 'Modèles de contrat', 'icon' => 'lease-templates', 'route' => 'admin.lease-templates.index', 'params' => []],
                    ['label' => 'Types de biens', 'icon' => 'property-types', 'route' => 'admin.property-types.index', 'params' => []],
                    ['label' => 'Utilisateurs Agence', 'icon' => 'admin', 'route' => 'admin.users.index', 'params' => []],
                ],
            ],
        ];
    }

    /**
     * Flat items fallback
     * @return array<int, array{label: string, icon: string, route: string, params: array<string, mixed>}>
     */
    public static function items(): array
    {
        $flat = [];
        foreach (self::groupedItems() as $group) {
            foreach ($group['items'] as $item) {
                $flat[] = $item;
            }
        }
        return $flat;
    }
}
