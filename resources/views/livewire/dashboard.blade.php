<div class="space-y-6">
    {{-- En-tête --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            Bonjour, {{ $userName }} 👋
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Agence : <span class="font-medium text-gray-700 dark:text-gray-300">{{ $agencyName }}</span>
            &mdash;
            Rôle : <span class="font-medium text-gray-700 dark:text-gray-300">{{ $roleName }}</span>
        </p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $kpis = [
            ['label' => 'Utilisateurs',   'value' => $usersCount,  'icon' => '👤', 'color' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'],
            ['label' => 'Propriétaires',  'value' => '—',           'icon' => '🏠', 'color' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'],
            ['label' => 'Locataires',     'value' => '—',           'icon' => '🧑', 'color' => 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'],
            ['label' => 'Contrats actifs','value' => '—',           'icon' => '📄', 'color' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'],
        ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 flex items-center gap-4 shadow-sm">
                <div class="text-3xl">{{ $kpi['icon'] }}</div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $kpi['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $kpi['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modules à venir --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Modules en cours d'implémentation</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @php
            $modules = [
                ['label' => 'Propriétaires', 'icon' => '👤', 'phase' => '2'],
                ['label' => 'Biens',          'icon' => '🏠', 'phase' => '2'],
                ['label' => 'Locataires',     'icon' => '🧑', 'phase' => '2'],
                ['label' => 'Contrats',       'icon' => '📄', 'phase' => '3'],
                ['label' => 'Loyers',         'icon' => '💰', 'phase' => '4'],
                ['label' => 'Cautions',       'icon' => '🔒', 'phase' => '4'],
                ['label' => 'Impayés',        'icon' => '⚠️', 'phase' => '5'],
                ['label' => 'Rapports',       'icon' => '📈', 'phase' => '6'],
            ];
            @endphp

            @foreach($modules as $module)
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $module['icon'] }}</span>
                    <span>{{ $module['label'] }}</span>
                    <x-badge variant="muted" class="ml-auto">Ph.{{ $module['phase'] }}</x-badge>
                </div>
            @endforeach
        </div>
    </div>
</div>
