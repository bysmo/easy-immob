<div class="space-y-8">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-800 to-slate-900 text-white p-6 sm:p-8 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-emerald-200 border border-white/20 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Espace Bailleur</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Bonjour, {{ auth()->user()->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-emerald-200">Bienvenue dans votre espace bailleur — {{ $owner->full_name }}</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $statItems = [
                ['label' => 'Total Biens', 'value' => $stats['total_properties'], 'color' => 'blue', 'icon' => 'properties'],
                ['label' => 'En Location', 'value' => $stats['properties_rented'], 'color' => 'emerald', 'icon' => 'tenants'],
                ['label' => 'Disponibles', 'value' => $stats['properties_available'], 'color' => 'teal', 'icon' => 'building'],
                ['label' => 'En Travaux', 'value' => $stats['properties_maintenance'], 'color' => 'amber', 'icon' => 'bell'],
                ['label' => 'Incidents ouverts', 'value' => $stats['pending_incidents'], 'color' => 'rose', 'icon' => 'bell'],
                ['label' => 'Mandats actifs', 'value' => $stats['contracts_active'], 'color' => 'indigo', 'icon' => 'leases'],
            ];
        @endphp
        @foreach ($statItems as $item)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-xs text-center">
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $item['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $item['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Reversements en attente --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-icon name="rents" class="w-5 h-5 text-emerald-600" />
                    Reversements en attente
                </h2>
                <a href="{{ route('owner-portal.financials') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Voir tout</a>
            </div>
            @forelse ($pendingPayouts as $payout)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100 dark:border-slate-800 last:border-0 text-sm">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $payout->period }}</p>
                        <p class="text-xs text-slate-500">Réf. {{ $payout->reference }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-emerald-700 dark:text-emerald-400">{{ number_format((float)$payout->net_amount, 0, ',', ' ') }} FCFA</p>
                        <p class="text-xs text-slate-400">Net bailleur</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-sm text-slate-400 py-6">Aucun reversement en attente.</p>
            @endforelse
        </div>

        {{-- Derniers incidents --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-icon name="bell" class="w-5 h-5 text-amber-600" />
                    Derniers Incidents
                </h2>
                <a href="{{ route('owner-portal.incidents') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Voir tout</a>
            </div>
            @forelse ($recentIncidents as $incident)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100 dark:border-slate-800 last:border-0 text-sm">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 dark:text-white truncate">{{ $incident->title }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $incident->property?->title }}</p>
                    </div>
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full border {{ $incident->status?->badgeClass() }}">
                        {{ $incident->status?->label() }}
                    </span>
                </div>
            @empty
                <p class="text-center text-sm text-slate-400 py-6">Aucun incident récent.</p>
            @endforelse
        </div>
    </div>

    {{-- Navigation rapide --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $quickLinks = [
                ['href' => route('owner-portal.properties'), 'label' => 'Mes Biens', 'icon' => 'properties', 'color' => 'from-blue-500 to-blue-600'],
                ['href' => route('owner-portal.incidents'), 'label' => 'Réparations', 'icon' => 'bell', 'color' => 'from-amber-500 to-orange-600'],
                ['href' => route('owner-portal.financials'), 'label' => 'Mes Finances', 'icon' => 'rents', 'color' => 'from-emerald-500 to-teal-600'],
                ['href' => route('owner-portal.contracts'), 'label' => 'Mandats', 'icon' => 'leases', 'color' => 'from-indigo-500 to-purple-600'],
            ];
        @endphp
        @foreach ($quickLinks as $link)
            <a href="{{ $link['href'] }}"
               class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $link['color'] }} p-5 text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                <x-icon :name="$link['icon']" class="w-7 h-7 text-white/80 mb-3 group-hover:scale-110 transition-transform" />
                <p class="text-sm font-bold">{{ $link['label'] }}</p>
            </a>
        @endforeach
    </div>
</div>
