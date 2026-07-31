<div class="space-y-8">
    
    @if (auth()->user()->isTenant())
        <!-- ================================================================= -->
        <!-- ESPACE LOCATAIRE DÉDIÉ                                            -->
        <!-- ================================================================= -->

        <!-- PWA Install Banner -->
        <livewire:components.pwa-install-banner />

        <!-- Banner Code Locataire & Bien Actuel -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-800 to-slate-900 text-white p-6 sm:p-8 shadow-xl">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-emerald-200 border border-white/20 text-xs font-semibold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Espace Citoyen Locataire</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                        Bonjour, {{ $userName }} 👋
                    </h1>
                    <p class="mt-1 text-xs text-emerald-200">Bienvenue dans votre espace de gestion locative.</p>
                </div>

                <!-- Carte Code Locataire -->
                <div x-data="{ copied: false }" class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-emerald-200 block font-semibold">Votre Code Locataire unique</span>
                        <span class="text-xl font-bold font-mono text-white block mt-0.5 tracking-wider">
                            {{ $tenant?->reference ?? 'Code indisponible' }}
                        </span>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $tenant?->reference }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-3.5 py-2 rounded-xl bg-white text-emerald-900 hover:bg-emerald-50 font-bold text-xs transition shadow-sm flex items-center gap-1.5 shrink-0">
                        <x-icon name="check" class="w-4 h-4" />
                        <span x-text="copied ? 'Copié !' : 'Copier le code'">Copier le code</span>
                    </button>
                </div>
            </div>
            
            <p class="mt-4 text-xs text-emerald-100/80 bg-black/20 p-3 rounded-xl border border-white/10">
                💡 <strong>Astuce :</strong> Communiquez votre code locataire (<strong>{{ $tenant?->reference }}</strong>) à votre agence immobilière pour vous rattacher directement à votre contrat de location.
            </p>
        </div>

        <!-- Grille Principale Espace Locataire -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Col 1 & 2 : Loyers, Échéances & Quittances -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Loyers & Quittances -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-icon name="wallet" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                <span>Suivi des Loyers & Quittances</span>
                            </h2>
                            <p class="text-xs text-slate-500">Consultez vos échéances et téléchargez vos quittances de paiement.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="py-3 px-3">Période</th>
                                    <th class="py-3 px-3">Échéance</th>
                                    <th class="py-3 px-3">Montant d'échéance</th>
                                    <th class="py-3 px-3">Statut</th>
                                    <th class="py-3 px-3 text-right">Quittance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 dark:divide-slate-800 text-xs">
                                @forelse ($tenantRentSchedules ?? [] as $schedule)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">
                                            {{ $schedule->period }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-600 dark:text-slate-400">
                                            {{ $schedule->due_date?->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-3 font-mono font-bold text-slate-900 dark:text-white">
                                            {{ number_format((float)$schedule->expected_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="py-3 px-3">
                                            @php
                                                $statusValue = is_object($schedule->status) ? $schedule->status->value : $schedule->status;
                                                $statusLabel = is_object($schedule->status) ? $schedule->status->label() : ucfirst($statusValue);
                                                $badgeClass = match($statusValue) {
                                                    'paid'           => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200',
                                                    'partially_paid' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border-blue-200',
                                                    'overdue'        => 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border-rose-200',
                                                    default          => 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border-amber-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border {{ $badgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            @if ($statusValue === 'paid')
                                                <a href="{{ route('rents.receipt.print', $schedule->id) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 font-semibold text-[11px] transition border border-emerald-200/60">
                                                    <x-icon name="document" class="w-3.5 h-3.5" />
                                                    <span>Quittance</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[11px] italic">Non disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                            Aucune échéance de loyer enregistrée pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Signalements & Incidents Récents -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-icon name="bell" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                <span>Incidents & Demandes de Réparation</span>
                            </h2>
                            <p class="text-xs text-slate-500">Signalez un problème dans votre logement et suivez sa résolution.</p>
                        </div>
                        <a href="{{ route('incidents.create') }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1">
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Nouveau signalement</span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($tenantIncidents ?? [] as $incident)
                            <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-slate-900 dark:text-white">{{ $incident->reference }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $incident->status->badgeClass() }}">
                                            {{ $incident->status->label() }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-1">{{ $incident->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Signalé le {{ $incident->created_at->format('d/m/Y') }}</p>
                                </div>
                                <a href="{{ route('incidents.show', $incident->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 font-semibold text-xs transition shrink-0">
                                    Voir détail
                                </a>
                            </div>
                        @empty
                            <p class="py-6 text-center text-slate-400 text-xs">Aucun incident signalé.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Col 3 : Mon Logement Actuel & Contacts -->
            <div class="space-y-6">
                
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Mon Logement Actuel</h3>

                    @php
                        $activeLease = ($tenantLeases ?? collect())->firstWhere('status.value', 'active') ?? ($tenantLeases ?? collect())->first();
                    @endphp

                    @if ($activeLease)
                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-slate-400 block">Bien loué :</span>
                                <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $activeLease->property?->title }}</span>
                                <p class="text-slate-500 mt-0.5">{{ $activeLease->property?->address }}, {{ $activeLease->property?->city }}</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 block">Montant du loyer mensuel :</span>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-base block">
                                    {{ number_format((float)$activeLease->total_monthly_amount, 0, ',', ' ') }} FCFA
                                </span>
                            </div>

                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 block">Référence contrat :</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200">{{ $activeLease->reference }}</span>
                            </div>

                            @if ($activeLease->agency)
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <span class="text-slate-400 block">Agence immobilière :</span>
                                    <span class="font-semibold text-slate-900 dark:text-white block">{{ $activeLease->agency->name }}</span>
                                    <p class="text-slate-500">{{ $activeLease->agency->email }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-6 text-center text-slate-400 text-xs">
                            <p>Vous n'avez pas encore de contrat de bail actif rattaché.</p>
                            <p class="mt-2 text-slate-500">Fournissez votre code locataire (<strong>{{ $tenant?->reference }}</strong>) à votre agence immobilière.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    @else
        <!-- ================================================================= -->
        <!-- DASHBOARD AGENCE                                                 -->
        <!-- ================================================================= -->

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
                                <span>Nouveau bailleur</span>
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

                <!-- KPI Bailleurs -->
                <x-card class="relative overflow-hidden group hover:border-emerald-500/40 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Bailleurs</p>
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

        <!-- Statistiques Financières & Recouvrement de l'Agence -->
        <x-card class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-icon name="wallet" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        <span>Statistiques Financières & Commissions</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Suivi consolidé des loyers attendus, encaissements, impayés, commissions et TVA à reverser.</p>
                </div>

                <!-- Selecteur de période -->
                <div class="flex items-center gap-2 shrink-0">
                    <label for="financialPeriod" class="text-xs font-semibold text-slate-600 dark:text-slate-400">Période :</label>
                    <select wire:model.live="financialPeriod" id="financialPeriod" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-bold focus:ring-emerald-500 py-1.5 px-3">
                        <option value="all">Toutes les périodes</option>
                        <option value="current_month">Mois en cours ({{ now()->format('m/Y') }})</option>
                        @foreach($availablePeriods ?? [] as $period)
                            <option value="{{ $period }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y') }} ({{ $period }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grille des 5 cartes financières -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- 1. Total Loyers Attendus -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Loyers Attendus</span>
                        <div class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                            <x-icon name="reports" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-extrabold font-mono tracking-tight text-slate-900 dark:text-white">
                        {{ number_format((float)($totalExpectedRent ?? 0), 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                    <p class="text-[11px] text-slate-500">Total échéanciers émises</p>
                </div>

                <!-- 2. Total Payés -->
                <div class="p-5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200/70 dark:border-emerald-800/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Loyers Payés</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                            <x-icon name="check" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-extrabold font-mono tracking-tight text-emerald-700 dark:text-emerald-300">
                        {{ number_format((float)($totalPaidRent ?? 0), 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Encaissements perçus</p>
                </div>

                <!-- 3. Total Impayés -->
                <div class="p-5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/40 border border-rose-200/70 dark:border-rose-800/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-800 dark:text-rose-300">Total Impayés</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-xs">
                            <x-icon name="arrears" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-extrabold font-mono tracking-tight text-rose-700 dark:text-rose-300">
                        {{ number_format((float)($totalUnpaidRent ?? 0), 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                    <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">Reste à recouvrir</p>
                </div>

                <!-- 4. Total Commission Agence -->
                <div class="p-5 rounded-2xl bg-teal-50/70 dark:bg-teal-950/40 border border-teal-200/70 dark:border-teal-800/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300">Commission Agence</span>
                        <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-xs">
                            <x-icon name="chart" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-extrabold font-mono tracking-tight text-teal-800 dark:text-teal-200">
                        {{ number_format((float)($totalCommission ?? 0), 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                    <p class="text-[11px] text-teal-600 dark:text-teal-400 font-medium">
                        Taux agence : <strong>{{ number_format((float)($commissionRate ?? 10), 1, ',', ' ') }} %</strong>
                    </p>
                </div>

                <!-- 5. Total TVA à Reverser -->
                <div class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/70 dark:border-amber-800/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300">TVA à Reverser</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                            <x-icon name="document" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-extrabold font-mono tracking-tight text-amber-800 dark:text-amber-300">
                        @if($isSubjectToTva ?? true)
                            {{ number_format((float)($totalTva ?? 0), 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                        @else
                            0 <span class="text-xs font-normal">FCFA</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 font-medium">
                        @if($isSubjectToTva ?? true)
                            TVA 18% sur commission
                        @else
                            Non assujetti à la TVA
                        @endif
                    </p>
                </div>
            </div>
        </x-card>

    @endif

</div>
