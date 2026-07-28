<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.saas-dashboard') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">&larr; Admin SaaS</a>
                <span class="text-xs text-slate-400">/ Agences Client</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Gestion des Agences Immobilières</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Supervision des agences d'immo, gestion de leurs forfaits et contrôle des accès.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <x-input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher nom, email..." class="w-full pl-10" />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-icon name="building" class="w-4 h-4" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-medium focus:ring-emerald-500">
                <option value="">Tous les statuts</option>
                <option value="active">Actif</option>
                <option value="suspended">Suspendu</option>
            </select>

            <select wire:model.live="planFilter" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-medium focus:ring-emerald-500">
                <option value="">Tous les forfaits</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table of Agencies -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold uppercase text-xs">
                    <tr>
                        <th class="p-4">Agence Immobilière</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Forfait Souscrit</th>
                        <th class="p-4">Biens Loués (Quota)</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($agencies as $agency)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-base">{{ $agency->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">ID: #{{ $agency->id }}</div>
                            </td>
                            <td class="p-4 text-xs">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $agency->email }}</div>
                                <div class="text-slate-400">{{ $agency->phone ?? 'Non renseigné' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-bold bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-300 rounded-lg">
                                    {{ $agency->subscriptionPlan?->name ?? 'Aucun' }}
                                </span>
                                <span class="block text-[11px] text-slate-400 uppercase font-semibold mt-1">
                                    {{ $agency->billing_cycle === 'yearly' ? 'Facturation Annuelle' : 'Facturation Mensuelle' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ $agency->properties_count }} / {{ $agency->max_properties_limit >= 99999 ? 'Illimité' : $agency->max_properties_limit }}
                                    </span>
                                    <span class="text-xs text-slate-400">({{ $agency->properties_usage_percentage }}%)</span>
                                </div>
                                <div class="w-32 h-2 bg-slate-100 dark:bg-slate-800 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full {{ $agency->hasReachedPropertyLimit() ? 'bg-rose-500' : 'bg-emerald-500' }} rounded-full" style="width: {{ $agency->properties_usage_percentage }}%"></div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($agency->status === 'active')
                                    <span class="px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full">Actif</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded-full">Suspendu</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="openEditModal({{ $agency->id }})" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-100 rounded-xl text-xs font-semibold transition">
                                    Changer Forfait
                                </button>
                                <button wire:click="toggleAgencyStatus({{ $agency->id }})" class="px-3 py-1.5 {{ $agency->status === 'active' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-100' }} rounded-xl text-xs font-semibold transition">
                                    {{ $agency->status === 'active' ? 'Suspendre' : 'Réactiver' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Aucune agence immobilière trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agencies->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $agencies->links() }}
            </div>
        @endif
    </div>

    <!-- Modal d'édition d'Abonnement -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl max-w-md w-full border border-slate-200/80 dark:border-slate-800 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Modifier le Forfait Agence</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label value="Sélectionner le Plan d'Abonnement" />
                        <select wire:model="newPlanId" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->isUnlimitedProperties() ? 'Biens Illimités' : $plan->max_properties . ' biens max' }}) - {{ number_format($plan->price_monthly, 0, ',', ' ') }} FCFA/mois</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label value="Cycle de Facturation" />
                        <select wire:model="newBillingCycle" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                            <option value="monthly">Mensuel</option>
                            <option value="yearly">Annuel (Economie d'abonnement)</option>
                        </select>
                    </div>

                    <div>
                        <x-label value="Statut du compte Agence" />
                        <select wire:model="newStatus" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                            <option value="active">Actif</option>
                            <option value="suspended">Suspendu</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button wire:click="$set('showEditModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Annuler</button>
                    <button wire:click="updateAgencySubscription" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Enregistrer & Générer Facture</button>
                </div>
            </div>
        </div>
    @endif
</div>
