<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.saas-dashboard') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">&larr; Admin SaaS</a>
                <span class="text-xs text-slate-400">/ Forfaits & Offres</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Gestion des Offres d'Abonnement SaaS</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configuration des tarifs, des quotas de biens et des fonctionnalités par forfait.</p>
        </div>

        <div>
            <button wire:click="openCreateModal" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                + Nouveau Forfait
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Cards des plans -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative flex flex-col justify-between">
                @if($plan->is_popular)
                    <div class="absolute -top-3 right-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] uppercase tracking-wider font-bold px-3 py-1 rounded-full shadow-xs">
                        Populaire
                    </div>
                @endif

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $plan->name }}</h2>
                        <span class="text-xs text-slate-400 font-mono">{{ $plan->agencies_count }} agences</span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 min-h-[36px]">{{ $plan->description }}</p>

                    <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl space-y-1">
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white">
                            {{ number_format($plan->price_monthly, 0, ',', ' ') }} FCFA <span class="text-xs font-normal text-slate-400">/ mois</span>
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ number_format($plan->price_yearly, 0, ',', ' ') }} FCFA / an (Facturation annuelle)
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 pt-2 border-t border-slate-200 dark:border-slate-700">
                            Quota : {{ $plan->isUnlimitedProperties() ? 'Nombre de biens ILLIMITÉ' : 'Jusqu\'à ' . $plan->max_properties . ' biens à louer' }}
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fonctionnalités incluses :</span>
                        <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1.5 pt-1">
                            @foreach(($plan->features ?? []) as $feature)
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs font-semibold {{ $plan->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $plan->is_active ? '• Actif' : '• Inactif' }}
                    </span>
                    <button wire:click="openEditModal({{ $plan->id }})" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition">
                        Éditer Forfait
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal d'édition/création de Plan -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl max-w-lg w-full border border-slate-200/80 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $editingPlanId ? 'Éditer le Forfait' : 'Nouveau Forfait' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label value="Nom du Forfait" />
                        <x-input type="text" wire:model="name" placeholder="ex: Pro Business" class="w-full mt-1" />
                    </div>

                    <div>
                        <x-label value="Description courte" />
                        <x-input type="text" wire:model="description" placeholder="Courte description de la cible..." class="w-full mt-1" />
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <x-label value="Quota Biens" />
                            <x-input type="number" wire:model="max_properties" class="w-full mt-1" />
                            <span class="text-[10px] text-slate-400">99999 = Illimité</span>
                        </div>
                        <div>
                            <x-label value="Prix Mensuel (FCFA)" />
                            <x-input type="number" wire:model="price_monthly" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label value="Prix Annuel (FCFA)" />
                            <x-input type="number" wire:model="price_yearly" class="w-full mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-label value="Fonctionnalités (1 par ligne)" />
                        <textarea wire:model="features_text" rows="4" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs focus:ring-emerald-500" placeholder="Biens illimités&#10;Support 24/7&#10;Export CSV"></textarea>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="is_active" class="rounded text-emerald-600 focus:ring-emerald-500">
                            Forfait Actif
                        </label>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="is_popular" class="rounded text-emerald-600 focus:ring-emerald-500">
                            Badge "Populaire"
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Annuler</button>
                    <button wire:click="savePlan" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Enregistrer</button>
                </div>
            </div>
        </div>
    @endif
</div>
