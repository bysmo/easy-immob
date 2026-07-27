<div class="space-y-8">
    
    <!-- Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 sm:p-8 shadow-xl shadow-slate-900/10">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Session Agence Active</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Bonjour, {{ $userName }} 👋
                </h1>
                <p class="mt-1.5 text-sm text-slate-300 flex items-center gap-2 flex-wrap">
                    <span>Agence : <strong class="text-white font-semibold">{{ $agencyName }}</strong></span>
                    <span class="text-slate-500">&bull;</span>
                    <span>Rôle : <strong class="text-emerald-400 font-semibold">{{ $roleName }}</strong></span>
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                @can('owners.create')
                    <a href="{{ route('owners.create') }}">
                        <x-button variant="primary" class="shadow-lg shadow-emerald-600/30">
                            <x-icon name="plus" class="w-4 h-4" />
                            <span>Nouveau propriétaire</span>
                        </x-button>
                    </a>
                @endcan
                <a href="{{ route('properties.index') }}">
                    <x-button variant="secondary" class="!bg-slate-800 !text-slate-100 !border-slate-700 hover:!bg-slate-700">
                        <x-icon name="building" class="w-4 h-4" />
                        <span>Explorer les biens</span>
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators (KPIs) Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <x-icon name="chart" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <span>Indicateurs Clés</span>
            </h2>
            <span class="text-xs font-medium text-slate-400">Actualisé en temps réel</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- KPI Utilisateurs -->
            <x-card class="relative overflow-hidden group hover:border-emerald-500/40 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Utilisateurs Agence</p>
                        <p class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $usersCount }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-100 dark:border-sky-900 flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
                        <x-icon name="users" class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-emerald-600 font-medium flex items-center gap-1">
                        <x-icon name="check" class="w-3.5 h-3.5" /> Actifs
                    </span>
                    <span class="text-slate-400">Équipe administrative</span>
                </div>
            </x-card>

            <!-- KPI Propriétaires -->
            <x-card class="relative overflow-hidden group hover:border-emerald-500/40 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Propriétaires</p>
                        <p class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $ownersCount }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900 flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
                        <x-icon name="owners" class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Portefeuille clients</span>
                    <a href="{{ route('owners.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold flex items-center gap-0.5">
                        Gérer &rarr;
                    </a>
                </div>
            </x-card>

            <!-- KPI Locataires -->
            <x-card class="relative overflow-hidden group hover:border-emerald-500/40 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Locataires Enregistrés</p>
                        <p class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $tenantsCount }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
                        <x-icon name="tenants" class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Occupants en cours</span>
                    <a href="{{ route('tenants.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold flex items-center gap-0.5">
                        Gérer &rarr;
                    </a>
                </div>
            </x-card>

            <!-- KPI Contrats Actifs -->
            <x-card class="relative overflow-hidden group hover:border-emerald-500/40 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Contrats Actifs</p>
                        <p class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $activeLeasesCount }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900 flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
                        <x-icon name="leases" class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Baux d'habitation</span>
                    <a href="{{ route('leases.index') }}" class="text-amber-600 dark:text-amber-400 hover:underline font-semibold flex items-center gap-0.5">
                        Voir les baux &rarr;
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Navigation rapide par Modules -->
    <x-card>
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Modules de Gestion Immobilier</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Accès rapide aux fonctionnalités et suivi du déploiement des modules.</p>
            </div>
            <x-badge color="indigo">Roadmap 2026</x-badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
            $modules = [
                ['label' => 'Propriétaires',   'icon' => 'owners',      'route' => 'owners.index',     'status' => 'Disponible',   'variant' => 'success'],
                ['label' => 'Biens Immobiliers','icon' => 'properties',  'route' => 'properties.index', 'status' => 'Disponible',   'variant' => 'success'],
                ['label' => 'Locataires',       'icon' => 'tenants',     'route' => 'tenants.index',    'status' => 'Disponible',   'variant' => 'success'],
                ['label' => 'Contrats de Bail', 'icon' => 'leases',      'route' => 'leases.index',     'status' => 'Disponible',   'variant' => 'success'],
                ['label' => 'Loyers & Échéances','icon' => 'rents',     'route' => 'rents.index',      'status' => 'Phase 4',      'variant' => 'muted'],
                ['label' => 'Cautions & Dépôts','icon' => 'deposits',    'route' => 'deposits.index',   'status' => 'Phase 4',      'variant' => 'muted'],
                ['label' => 'Gestion Impayés',  'icon' => 'arrears',     'route' => 'arrears.index',    'status' => 'Phase 5',      'variant' => 'muted'],
                ['label' => 'Rapports Financiers','icon' => 'reports',   'route' => 'reports.index',    'status' => 'Phase 6',      'variant' => 'muted'],
            ];
            @endphp

            @foreach($modules as $m)
                @php
                    $routeUrl = \Illuminate\Support\Facades\Route::has($m['route']) ? route($m['route']) : '#';
                @endphp
                <a href="{{ $routeUrl }}" class="flex items-center justify-between p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 hover:bg-white dark:hover:bg-slate-800 hover:border-emerald-500/40 hover:shadow-xs transition-all duration-150 group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            <x-icon :name="$m['icon']" class="w-5 h-5" />
                        </div>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">{{ $m['label'] }}</span>
                    </div>
                    <x-badge :variant="$m['variant']">{{ $m['status'] }}</x-badge>
                </a>
            @endforeach
        </div>
    </x-card>

</div>
