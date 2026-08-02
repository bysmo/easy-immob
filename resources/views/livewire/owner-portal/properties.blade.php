<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mes Biens Immobiliers</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Statut de vos biens par agence</p>
        </div>

        {{-- Filtres --}}
        <div class="flex gap-3 flex-wrap">
            <select wire:model.live="statusFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Tous les statuts</option>
                <option value="available">Disponible</option>
                <option value="rented">En location</option>
                <option value="maintenance">En travaux</option>
            </select>

            <select wire:model.live="agencyFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Toutes les agences</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($grouped->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800">
            <x-icon name="building" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
            <p class="text-slate-500">Aucun bien ne correspond à vos filtres.</p>
        </div>
    @else
        @foreach ($grouped as $agencyName => $properties)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center gap-3">
                    <x-icon name="building" class="w-5 h-5 text-emerald-600" />
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $agencyName }}</h2>
                    <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">{{ $properties->count() }} bien(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 dark:bg-slate-800/30">
                                <th class="py-3 px-4">Référence</th>
                                <th class="py-3 px-4">Bien</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Loyer HC</th>
                                <th class="py-3 px-4 text-right">IRF</th>
                                <th class="py-3 px-4 text-right">Commission</th>
                                <th class="py-3 px-4 text-right">Net Bailleur</th>
                                <th class="py-3 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @foreach ($properties as $property)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $property->reference }}</td>
                                    <td class="py-3 px-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $property->title }}</p>
                                        <p class="text-xs text-slate-400">{{ $property->city }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-500">{{ $property->propertyType?->name ?? '—' }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $statusClasses = [
                                                'available'   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'rented'      => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'maintenance' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            ];
                                            $statusLabels = [
                                                'available'   => 'Disponible',
                                                'rented'      => 'En location',
                                                'maintenance' => 'En travaux',
                                            ];
                                            $statusVal = $property->status instanceof \BackedEnum ? $property->status->value : (string)$property->status;
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $statusClasses[$statusVal] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                            {{ $statusLabels[$statusVal] ?? $statusVal }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold text-slate-900 dark:text-white">
                                        {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 px-4 text-right text-rose-600 text-xs">
                                        {{ $property->is_subject_to_irf ? '− ' . number_format($property->irf_amount, 0, ',', ' ') . ' F' : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-right text-amber-600 text-xs">
                                        − {{ number_format($property->agency_fee_amount, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($property->net_owner_income, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <button wire:click="viewProperty({{ $property->id }})"
                                                class="px-2.5 py-1 rounded-lg text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1.5 text-xs font-semibold cursor-pointer">
                                            <x-icon name="eye" class="w-3.5 h-3.5" />
                                            <span>Fiche</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Modale Fiche du bien --}}
    @if ($showPropertyModal && $selectedProperty)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden my-auto">
                <!-- En-tête de la modale -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-700 dark:text-emerald-300">
                            <x-icon name="building" class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $selectedProperty->title }}</h3>
                                <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold">
                                    {{ $selectedProperty->reference }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $selectedProperty->city }} @if($selectedProperty->neighborhood) • {{ $selectedProperty->neighborhood }} @endif
                            </p>
                        </div>
                    </div>
                    <button wire:click="closePropertyModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Corps de la modale (scrollable) -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    
                    <!-- Galerie photos -->
                    @if(count($selectedProperty->photo_list) > 0)
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 aspect-video relative group">
                            <img src="{{ $selectedProperty->photo_list[0] }}" class="w-full h-full object-cover">
                            <div class="absolute bottom-3 left-3 bg-slate-900/70 text-white text-xs px-2.5 py-1 rounded-full backdrop-blur-xs font-medium">
                                {{ count($selectedProperty->photo_list) }} photo(s) disponible(s)
                            </div>
                        </div>
                    @endif

                    <!-- Infos générales -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Type</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $selectedProperty->propertyType?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Surface</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $selectedProperty->surface_area ? $selectedProperty->surface_area . ' m²' : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Pièces / Chambres</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $selectedProperty->bedrooms ? $selectedProperty->bedrooms . ' ch. / ' . ($selectedProperty->bathrooms ?? 0) . ' sdb' : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Statut Actuel</span>
                            @php
                                $stVal = $selectedProperty->status instanceof \BackedEnum ? $selectedProperty->status->value : (string)$selectedProperty->status;
                                $stLbl = ['available' => 'Disponible', 'rented' => 'En location', 'maintenance' => 'En travaux'];
                            @endphp
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $stLbl[$stVal] ?? $stVal }}</span>
                        </div>
                    </div>

                    <!-- Localisation / Adresse -->
                    @if($selectedProperty->address)
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Adresse & Localisation</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">{{ $selectedProperty->address }}</p>
                        </div>
                    @endif

                    <!-- Récapitulatif Financier du Bien -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Répartition Financière Mensuelle</h4>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800 text-xs overflow-hidden">
                            <div class="p-3 bg-white dark:bg-slate-900 flex justify-between items-center">
                                <span class="text-slate-600 dark:text-slate-400 font-medium">Loyer Mensuel HC</span>
                                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ number_format((float)$selectedProperty->rent_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="p-3 bg-slate-50/50 dark:bg-slate-800/30 flex justify-between items-center">
                                <span class="text-slate-600 dark:text-slate-400 font-medium">Impôt sur Revenu Foncier (IRF)</span>
                                <span class="font-semibold text-rose-600 dark:text-rose-400">
                                    {{ $selectedProperty->is_subject_to_irf ? '− ' . number_format($selectedProperty->irf_amount, 0, ',', ' ') . ' FCFA' : 'Exonéré' }}
                                </span>
                            </div>
                            <div class="p-3 bg-white dark:bg-slate-900 flex justify-between items-center">
                                <span class="text-slate-600 dark:text-slate-400 font-medium">Commission de Gestion Agence</span>
                                <span class="font-semibold text-amber-600 dark:text-amber-400">
                                    − {{ number_format($selectedProperty->agency_fee_amount, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            <div class="p-3 bg-emerald-50/60 dark:bg-emerald-950/40 flex justify-between items-center text-emerald-900 dark:text-emerald-200">
                                <span class="font-bold">Revenu Net Bailleur Estimé</span>
                                <span class="font-bold text-sm text-emerald-700 dark:text-emerald-300">{{ number_format($selectedProperty->net_owner_income, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agence de gestion -->
                    @if($selectedProperty->managementContract?->agency)
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Agence Mandataire</h4>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $selectedProperty->managementContract->agency->name }}</p>
                                    <p class="text-slate-500 mt-0.5">{{ $selectedProperty->managementContract->agency->email ?? $selectedProperty->managementContract->agency->phone }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-semibold text-[11px]">
                                    Mandat Actif
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Entretien / Réparations -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Cumul Coût Réparations & Entretien</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ number_format($selectedProperty->total_maintenance_cost, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                </div>

                <!-- Pied de modale -->
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex justify-end shrink-0">
                    <button wire:click="closePropertyModal" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs hover:bg-slate-300 dark:hover:bg-slate-700 transition cursor-pointer">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
