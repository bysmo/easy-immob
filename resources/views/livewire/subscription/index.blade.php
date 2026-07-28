<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full">Offres & Formules</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Gestion de la souscription</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Mon Abonnement EasyImmob</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consultez votre formule actuelle, votre quota de biens disponibles et vos factures.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if($agency)
        <!-- Current Subscription & Usage Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Abonnement Actuel -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-850 to-emerald-950 text-white p-6 rounded-2xl shadow-xl relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 text-xs font-bold bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 uppercase tracking-wider">Formule Actuelle</span>
                        <span class="text-xs font-semibold text-emerald-400 uppercase">● {{ ucfirst($agency->subscription_status ?? 'Actif') }}</span>
                    </div>

                    <h2 class="text-2xl font-black mt-4 text-white">
                        {{ $agency->subscriptionPlan?->name ?? 'Formule Standard' }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Facturation {{ $agency->billing_cycle === 'yearly' ? 'Annuelle' : 'Mensuelle' }} — {{ number_format($agency->subscriptionPlan?->getPriceForCycle($agency->billing_cycle) ?? 0, 0, ',', ' ') }} FCFA / {{ $agency->billing_cycle === 'yearly' ? 'an' : 'mois' }}
                    </p>

                    <div class="mt-6 pt-4 border-t border-slate-700/60 text-xs text-slate-300 space-y-2">
                        <div class="flex justify-between">
                            <span>Limite de biens gérés :</span>
                            <span class="font-bold text-white">{{ $agency->max_properties_limit >= 99999 ? 'Illimitée' : $agency->max_properties_limit . ' biens' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Prochain renouvellement :</span>
                            <span class="font-bold text-white">{{ $agency->subscription_ends_at?->format('d/m/Y') ?? 'Renouvellement automatique' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-700/60 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Agence : {{ $agency->name }}</span>
                    <span class="text-emerald-400 font-bold">Inscrit depuis {{ $agency->created_at->format('m/Y') }}</span>
                </div>
            </div>

            <!-- Gauge de Consommation de Quota -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Utilisation du Quota de Biens à Louer</h3>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $agency->properties_count }} / {{ $agency->max_properties_limit >= 99999 ? 'Illimité' : $agency->max_properties_limit }} biens enregistrés
                        </span>
                    </div>

                    <!-- Progress bar -->
                    <div class="w-full h-4 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5 border border-slate-200 dark:border-slate-700">
                        <div class="h-full rounded-full transition-all duration-700 {{ $agency->hasReachedPropertyLimit() ? 'bg-rose-500' : ($agency->properties_usage_percentage > 75 ? 'bg-amber-500' : 'bg-gradient-to-r from-emerald-500 to-teal-500') }}" 
                             style="width: {{ $agency->properties_usage_percentage }}%"></div>
                    </div>

                    <div class="flex justify-between items-center text-xs mt-2 font-medium text-slate-500 dark:text-slate-400">
                        <span>Taux de consommation : {{ $agency->properties_usage_percentage }}%</span>
                        <span>
                            @if($agency->max_properties_limit >= 99999)
                                Places restantes : Biens illimités
                            @else
                                Places restantes : <strong class="text-slate-900 dark:text-white">{{ $agency->remaining_properties_count }} bien(s)</strong>
                            @endif
                        </span>
                    </div>

                    @if($agency->hasReachedPropertyLimit())
                        <div class="mt-5 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl flex items-start gap-3 text-rose-800 dark:text-rose-300 text-xs">
                            <x-icon name="bell" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                            <div>
                                <strong class="block text-sm font-bold">Quota Maximum de Biens Atteint !</strong>
                                Vous avez atteint la limite de votre forfait actuel ({{ $agency->max_properties_limit }} biens). Pour ajouter de nouveaux biens à louer, veuillez surclasser votre abonnement vers une formule supérieure.
                            </div>
                        </div>
                    @elseif($agency->properties_usage_percentage >= 80)
                        <div class="mt-5 p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl flex items-start gap-3 text-amber-800 dark:text-amber-300 text-xs">
                            <x-icon name="bell" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                            <div>
                                <strong class="block text-sm font-bold">Attention, Quota Proche de la Limite</strong>
                                Vous avez utilisé {{ $agency->properties_usage_percentage }}% de la capacité de votre forfait. Pensez à anticiper la montée en gamme.
                            </div>
                        </div>
                    @else
                        <div class="mt-5 p-4 bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900 rounded-xl flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-300">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Votre formule actuelle convient parfaitement à l'activité de votre agence.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pricing Grid & Plan Chooser -->
        <div class="space-y-6 pt-4">
            <div class="text-center max-w-xl mx-auto">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Choisir ou Changer de Formule d'Abonnement</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Des forfaits évolutifs selon le volume de biens immobiliers gérés par votre agence.</p>

                <!-- Billing Cycle Selector (Mensuel / Annuel) -->
                <div class="mt-6 inline-flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <button wire:click="$set('selectedCycle', 'monthly')" 
                            class="px-5 py-2 text-xs font-bold rounded-xl transition {{ $selectedCycle === 'monthly' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        Facturation Mensuelle
                    </button>
                    <button wire:click="$set('selectedCycle', 'yearly')" 
                            class="px-5 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 {{ $selectedCycle === 'yearly' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        Facturation Annuelle
                        <span class="px-2 py-0.5 text-[10px] bg-amber-400 text-slate-950 font-black rounded-md uppercase">2 mois offerts</span>
                    </button>
                </div>
            </div>

            <!-- Cards de Forfaits -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    @php
                        $isCurrent = $agency->subscription_plan_id === $plan->id && $agency->billing_cycle === $selectedCycle;
                        $price = $plan->getPriceForCycle($selectedCycle);
                        $isFreeTrial = $plan->slug === 'essai-gratuit' || ($plan->price_monthly == 0 && $plan->price_yearly == 0);
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border transition-all duration-300 p-6 flex flex-col justify-between relative {{ $plan->is_popular ? 'border-emerald-500 shadow-lg ring-1 ring-emerald-500' : 'border-slate-200/80 dark:border-slate-800 shadow-sm hover:border-slate-300' }}">
                        @if($plan->is_popular)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-full shadow-xs">
                                Offre la plus choisie
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $plan->name }}</h3>
                                @if($isCurrent)
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full uppercase">Formule Actuelle</span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 min-h-[36px]">{{ $plan->description }}</p>

                            <div class="mt-5 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                                <div class="text-2xl font-black text-slate-900 dark:text-white">
                                    {{ number_format($price, 0, ',', ' ') }} <span class="text-xs font-normal text-slate-500">FCFA / {{ $selectedCycle === 'yearly' ? 'an' : 'mois' }}</span>
                                </div>
                                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                                    Capacity : {{ $plan->isUnlimitedProperties() ? 'Biens ILLIMITÉS' : 'Jusqu\'à ' . $plan->max_properties . ' biens' }}
                                </div>
                            </div>

                            <div class="mt-6 space-y-2">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avantages :</span>
                                <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-2 pt-1">
                                    @foreach(($plan->features ?? []) as $feature)
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="mt-8 pt-4">
                            @if($isCurrent)
                                <button disabled class="w-full py-2.5 text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl cursor-not-allowed text-center">
                                    Votre Formule Actuelle
                                </button>
                            @elseif($isFreeTrial)
                                <button wire:click="requestPlanChange({{ $plan->id }})"
                                        class="w-full py-2.5 text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center">
                                    Offre d'essai initiale
                                </button>
                            @else
                                <button wire:click="requestPlanChange({{ $plan->id }})" 
                                        class="w-full py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-98 rounded-xl shadow-md shadow-emerald-600/20 transition text-center">
                                    Changer & Passer à cette Formule
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Agency Subscription Invoices Table -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Historique de mes Factures d'Abonnement</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Consultez et téléchargez vos factures SaaS EasyImmob.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold uppercase">
                        <tr>
                            <th class="p-3">N° Facture</th>
                            <th class="p-3">Formule Souscrite</th>
                            <th class="p-3">Cycle</th>
                            <th class="p-3">Date d'Émission</th>
                            <th class="p-3">Montant Total</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3 text-right">Télécharger</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $invoice->number }}
                                </td>
                                <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $invoice->subscriptionPlan?->name ?? 'Forfait SaaS' }}
                                </td>
                                <td class="p-3 uppercase text-[11px] font-semibold text-slate-500">
                                    {{ $invoice->billing_cycle === 'yearly' ? 'Annuel' : 'Mensuel' }}
                                </td>
                                <td class="p-3">
                                    {{ $invoice->invoice_date?->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $invoice->formatted_total }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $invoice->status_badge_class }}">
                                        {{ $invoice->status_label }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.saas-invoices.print', $invoice->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-medium transition inline-flex items-center gap-1">
                                        <x-icon name="reports" class="w-3.5 h-3.5 text-slate-500" />
                                        Facture PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">Aucune facture d'abonnement enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MODALE 1 : Confirmation de changement de forfait -->
    @if($showConfirmModal && $targetPlan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Confirmer le Changement de Forfait</h3>
                        <p class="text-xs text-slate-500">Validation de la souscription</p>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl space-y-3 border border-slate-100 dark:border-slate-700/60 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Formule sélectionnée :</span>
                        <strong class="text-slate-900 dark:text-white font-bold">{{ $targetPlan->name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cycle de facturation :</span>
                        <strong class="text-slate-900 dark:text-white uppercase font-bold">{{ $selectedCycle === 'yearly' ? 'Annuel' : 'Mensuel' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Capacité de biens :</span>
                        <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $targetPlan->isUnlimitedProperties() ? 'Illimitée' : $targetPlan->max_properties . ' biens' }}</strong>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-sm">
                        <span class="font-bold text-slate-700 dark:text-slate-300">Montant à régler :</span>
                        <strong class="text-emerald-600 font-black">{{ number_format($targetPlan->getPriceForCycle($selectedCycle), 0, ',', ' ') }} FCFA</strong>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    En confirmant, votre abonnement agence sera mis à jour immédiatement et une facture d'abonnement sera générée.
                </p>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button wire:click="closeModals" 
                            class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition">
                        Annuler
                    </button>
                    <button wire:click="executePlanChange" 
                            class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-98 rounded-xl shadow-md shadow-emerald-600/20 transition">
                        Confirmer & Passer au Forfait
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODALE 2 : Alerte d'Erreur (Rétrogradation / Quota dépassé / Plan gratuit interdit) -->
    @if($showErrorModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-rose-200 dark:border-rose-900 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Action Non Autorisée</h3>
                        <p class="text-xs text-rose-500 font-semibold">Restriction de rétrogradation / Quota de biens</p>
                    </div>
                </div>

                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-xl text-xs text-rose-800 dark:text-rose-300 leading-relaxed font-medium">
                    {{ $errorMessage }}
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button wire:click="closeModals" 
                            class="px-5 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition">
                        Compris, Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
