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
                        ['label' => 'Configuration Mails SMTP', 'icon' => 'notifications', 'route' => 'admin.mail-settings.index', 'params' => []],
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

        // 3. Le Bailleur voit son portail propriétaire
        if ($user && $user->isOwner()) {
            return [
                [
                    'section' => 'Mon Espace Bailleur',
                    'items'   => [
                        ['label' => 'Tableau de bord',       'icon' => 'dashboard',    'route' => 'owner-portal.dashboard',   'params' => []],
                        ['label' => 'Mes Biens',             'icon' => 'properties',   'route' => 'owner-portal.properties',  'params' => []],
                        ['label' => 'Réparations',          'icon' => 'bell',         'route' => 'owner-portal.incidents',   'params' => []],
                        ['label' => 'Mes Finances',          'icon' => 'rents',        'route' => 'owner-portal.financials',  'params' => []],
                        ['label' => 'Mandats de Gestion',    'icon' => 'leases',       'route' => 'owner-portal.contracts',   'params' => []],
                    ],
                ],
            ];
        }

        // 3. L'Agence Immobilière – sections filtrées par permission
        $groups = [];

        // ---------------------------------------------------------------
        // Pilotage (tout le monde dans l'agence)
        // ---------------------------------------------------------------
        $groups[] = [
            'section' => 'Pilotage',
            'items'   => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'params' => []],
                ['label' => 'Messagerie Locataires', 'icon' => 'notifications', 'route' => 'inquiries.index', 'params' => []],
            ],
        ];

        // ---------------------------------------------------------------
        // Gestion Locative (owners.view | properties.view | tenants.view)
        // ---------------------------------------------------------------
        $locativeItems = [];

        if ($user?->can('owners.view')) {
            $locativeItems[] = ['label' => 'Bailleurs', 'icon' => 'owners', 'route' => 'owners.index', 'params' => []];
            $locativeItems[] = ['label' => 'Mandats de Gestion', 'icon' => 'leases', 'route' => 'management-contracts.index', 'params' => []];
        }

        if ($user?->can('properties.view')) {
            $locativeItems[] = ['label' => 'Biens Immobiliers', 'icon' => 'properties', 'route' => 'properties.index', 'params' => []];
        }

        if ($user?->can('tenants.view')) {
            $locativeItems[] = ['label' => 'Locataires', 'icon' => 'tenants', 'route' => 'tenants.index', 'params' => []];
        }

        if ($user?->can('leases.view')) {
            $locativeItems[] = ['label' => 'Contrats de Bail', 'icon' => 'leases', 'route' => 'leases.index', 'params' => []];
        }

        if ($user?->can('incidents.view')) {
            $locativeItems[] = ['label' => 'Incidents & Réparations', 'icon' => 'bell', 'route' => 'incidents.index', 'params' => []];
        }

        if (! empty($locativeItems)) {
            $groups[] = ['section' => 'Gestion Locative', 'items' => $locativeItems];
        }

        // ---------------------------------------------------------------
        // Finances & Recouvrement (rents.view | deposits.view | arrears.view)
        // ---------------------------------------------------------------
        $financeItems = [];

        if ($user?->can('rents.view')) {
            $financeItems[] = ['label' => 'Loyers & Échéances', 'icon' => 'rents', 'route' => 'rents.index', 'params' => []];
            $financeItems[] = ['label' => 'Reversements Bailleurs', 'icon' => 'owners', 'route' => 'owners.payouts.index', 'params' => []];
        }

        if ($user?->can('deposits.view')) {
            $financeItems[] = ['label' => 'Cautions & Dépôts', 'icon' => 'deposits', 'route' => 'deposits.index', 'params' => []];
        }

        if ($user?->can('arrears.view')) {
            $financeItems[] = ['label' => 'Gestion des Impayés', 'icon' => 'arrears', 'route' => 'arrears.index', 'params' => []];
        }

        if (! empty($financeItems)) {
            $groups[] = ['section' => 'Finances & Recouvrement', 'items' => $financeItems];
        }

        // ---------------------------------------------------------------
        // Suivi & Rapports (notifications.view | reports.view)
        // ---------------------------------------------------------------
        $reportItems = [];

        if ($user?->can('notifications.view')) {
            $reportItems[] = ['label' => 'Notifications', 'icon' => 'notifications', 'route' => 'notifications.index', 'params' => []];
        }

        if ($user?->can('reports.view')) {
            $reportItems[] = ['label' => 'Rapports', 'icon' => 'reports', 'route' => 'reports.index', 'params' => []];
        }

        if (! empty($reportItems)) {
            $groups[] = ['section' => 'Suivi & Rapports', 'items' => $reportItems];
        }

        // ---------------------------------------------------------------
        // Administration Agence (users.view = Administrateur uniquement)
        // ---------------------------------------------------------------
        if ($user?->can('users.view')) {
            $groups[] = [
                'section' => 'Administration Agence',
                'items'   => [
                    ['label' => 'Informations Agence', 'icon' => 'building', 'route' => 'agency.settings', 'params' => []],
                    ['label' => 'Mon Abonnement', 'icon' => 'rents', 'route' => 'subscription.index', 'params' => []],
                    ['label' => 'Modèles de contrat', 'icon' => 'lease-templates', 'route' => 'admin.lease-templates.index', 'params' => []],
                    ['label' => 'Types de biens', 'icon' => 'property-types', 'route' => 'admin.property-types.index', 'params' => []],
                    ['label' => 'Utilisateurs Agence', 'icon' => 'admin', 'route' => 'admin.users.index', 'params' => []],
                ],
            ];
        }

        return $groups;
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
