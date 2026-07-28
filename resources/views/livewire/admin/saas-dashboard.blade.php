<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full">SaaS Super Admin</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Plateforme EasyImmob</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Tableau de Bord Administrateur SaaS</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Vue d'ensemble des agences immobilières client, revenus récurrents et activité de la plateforme.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.agencies.index') }}" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <x-icon name="building" class="w-4 h-4" />
                Gérer les Agences
            </a>
            <a href="{{ route('admin.saas-invoices.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition flex items-center gap-2">
                <x-icon name="rents" class="w-4 h-4" />
                Factures SaaS
            </a>
        </div>
    </div>

    <!-- KPIs Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- MRR -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Revenu Mensuel (MRR)</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-icon name="rents" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ number_format($mrr, 0, ',', ' ') }} FCFA
                </div>
                <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                    <span>ARR estimé : {{ number_format($arr, 0, ',', ' ') }} FCFA/an</span>
                </div>
            </div>
        </div>

        <!-- Agences Client -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agences Immobilieres</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <x-icon name="owners" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ $totalAgencies }} agences
                </div>
                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                    <span class="text-emerald-600 font-semibold">{{ $activeAgencies }} actives</span> • <span class="text-rose-500">{{ $suspendedAgencies }} suspendues</span>
                </div>
            </div>
        </div>

        <!-- Total Biens SaaS -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Biens Gérés (Global SaaS)</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <x-icon name="properties" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ $totalSaasProperties }} biens
                </div>
                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Sous contrat locatif sur la plateforme
                </div>
            </div>
        </div>

        <!-- Encaissements SaaS -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Facturation Encaissée</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <x-icon name="reports" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ number_format($totalPaidInvoicesAmount, 0, ',', ' ') }} FCFA
                </div>
                <div class="text-xs font-medium text-amber-600 dark:text-amber-400 mt-1">
                    En attente : {{ number_format($totalPendingInvoicesAmount, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Distribution des forfaits & Agences récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Distribution des Forfaits -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Répartition des Packages</h2>
                    <a href="{{ route('admin.plans.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">Gérer Forfaits</a>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Agences réparties selon leur formule d'abonnement.</p>

                <div class="space-y-4">
                    @foreach($plansStats as $plan)
                        @php
                            $pct = $totalAgencies > 0 ? round(($plan->agencies_count / $totalAgencies) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm font-semibold mb-1">
                                <span class="text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                    {{ $plan->name }}
                                    @if($plan->is_popular)
                                        <span class="px-2 py-0.5 text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 rounded-md">Populaire</span>
                                    @endif
                                </span>
                                <span class="text-slate-600 dark:text-slate-400">{{ $plan->agencies_count }} agences ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">
                                Limite : {{ $plan->isUnlimitedProperties() ? 'Illimitée' : $plan->max_properties . ' biens' }} | {{ number_format($plan->price_monthly, 0, ',', ' ') }} FCFA/mois
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Liste des Agences Récentes -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Dernières Agences Inscrites</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Suivi des abonnements et quotas de biens consommés.</p>
                </div>
                <a href="{{ route('admin.agencies.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">Voir toutes les agences &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold uppercase">
                        <tr>
                            <th class="p-3">Agence</th>
                            <th class="p-3">Forfait</th>
                            <th class="p-3">Biens Utilisés</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentAgencies as $agency)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $agency->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $agency->email }}</div>
                                </td>
                                <td class="p-3">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $agency->subscriptionPlan?->name ?? 'Aucun' }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 uppercase">
                                        {{ $agency->billing_cycle === 'yearly' ? 'Annuel' : 'Mensuel' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $agency->properties_count }} / {{ $agency->max_properties_limit >= 99999 ? '∞' : $agency->max_properties_limit }}
                                    </div>
                                    <div class="w-24 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $agency->properties_usage_percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    @if($agency->status === 'active')
                                        <span class="px-2 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full">Actif</span>
                                    @else
                                        <span class="px-2 py-1 text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded-full">Suspendu</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.agencies.index') }}" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-medium transition">
                                        Gérer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-400">Aucune agence enregistrée pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Invoices Table -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Dernières Factures SaaS Générées</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Factures d'abonnement émises pour les agences immobilières.</p>
            </div>
            <a href="{{ route('admin.saas-invoices.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">Toutes les factures SaaS &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold uppercase">
                    <tr>
                        <th class="p-3">N° Facture</th>
                        <th class="p-3">Agence</th>
                        <th class="p-3">Période / Cycle</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Montant Total</th>
                        <th class="p-3">Statut</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentInvoices as $invoice)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                                {{ $invoice->number }}
                            </td>
                            <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $invoice->agency?->name }}
                            </td>
                            <td class="p-3">
                                {{ $invoice->subscriptionPlan?->name }} ({{ $invoice->billing_cycle === 'yearly' ? 'Annuel' : 'Mensuel' }})
                            </td>
                            <td class="p-3">
                                {{ $invoice->invoice_date?->format('d/m/Y') }}
                            </td>
                            <td class="p-3 font-bold text-slate-900 dark:text-white">
                                {{ $invoice->formatted_total }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $invoice->status_badge_class }}">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.saas-invoices.print', $invoice->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-medium transition inline-flex items-center gap-1">
                                    <x-icon name="reports" class="w-3 h-3" />
                                    Facture PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-slate-400">Aucune facture enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
