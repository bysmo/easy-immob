<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Réparations & Incidents</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Suivez et approuvez les réparations sur vos biens</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <select wire:model.live="statusFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="propertyFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Tous les biens</option>
                @foreach ($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($incidents->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800">
            <x-icon name="bell" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
            <p class="text-slate-500">Aucun incident enregistré.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 dark:bg-slate-800/30">
                            <th class="py-3 px-4">Bien</th>
                            <th class="py-3 px-4">Incident</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 px-4 text-right">Coût réparation</th>
                            <th class="py-3 px-4 text-right">Montant approuvé</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @foreach ($incidents as $incident)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $incident->property?->title ?? '—' }}</p>
                                </td>
                                <td class="py-3 px-4 max-w-xs">
                                    <p class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $incident->title }}</p>
                                    @if ($incident->repair_details)
                                        <p class="text-xs text-slate-400 truncate">{{ $incident->repair_details }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-xs px-2 py-0.5 rounded-full border {{ $incident->status?->badgeClass() }}">
                                        {{ $incident->status?->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-slate-900 dark:text-white">
                                    @if ($incident->repair_cost)
                                        {{ number_format((float)$incident->repair_cost, 0, ',', ' ') }} F
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if ($incident->owner_confirmed_at)
                                        <p class="font-bold text-emerald-700 dark:text-emerald-400">{{ number_format((float)$incident->owner_confirmed_amount, 0, ',', ' ') }} F</p>
                                        <p class="text-[10px] text-slate-400">{{ $incident->owner_confirmed_at->format('d/m/Y') }}</p>
                                    @else
                                        <span class="text-slate-400 text-xs">Non confirmé</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-500">
                                    {{ $incident->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="viewIncident({{ $incident->id }})"
                                                class="px-2.5 py-1.5 rounded-xl text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition inline-flex items-center gap-1.5 text-xs font-semibold cursor-pointer">
                                            <x-icon name="eye" class="w-3.5 h-3.5 text-emerald-600" />
                                            <span>Fiche</span>
                                        </button>

                                        @if ($incident->status?->value === 'resolved' && ! $incident->owner_confirmed_at)
                                            <button wire:click="confirmRepair({{ $incident->id }})"
                                                    class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition cursor-pointer">
                                                Décider
                                            </button>
                                        @elseif ($incident->status?->value === 'rejected')
                                            <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1">
                                                <x-icon name="x" class="w-3.5 h-3.5" /> Rejeté
                                            </span>
                                        @elseif ($incident->owner_confirmed_at)
                                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                                <x-icon name="check" class="w-3.5 h-3.5" /> Approuvé
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal Fiche Réparation / Incident --}}
    @if ($showConfirmModal && $selectedIncident)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden my-auto">
                <!-- Header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-amber-700 dark:text-amber-300">
                            <x-icon name="bell" class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Fiche d'Incident & Réparation</h3>
                                <span class="text-xs px-2.5 py-0.5 rounded-full border font-semibold {{ $selectedIncident->status?->badgeClass() }}">
                                    {{ $selectedIncident->status?->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Réf: {{ $selectedIncident->reference }} • Signalé le {{ $selectedIncident->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <button wire:click="closeConfirmModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Body (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1 text-sm">
                    
                    <!-- Informations du bien & agence -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Bien immobilier</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block text-sm">{{ $selectedIncident->property?->title ?? '—' }}</span>
                            <span class="text-slate-500 block">{{ $selectedIncident->property?->city }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Agence / Locataire</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $selectedIncident->agency?->name ?? '—' }}</span>
                            <span class="text-slate-500 block">Locataire: {{ $selectedIncident->tenant?->full_name ?? 'Non spécifié' }}</span>
                        </div>
                    </div>

                    <!-- Description du problème -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Problème Signalé</h4>
                        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-2">
                            <h5 class="font-bold text-slate-900 dark:text-white">{{ $selectedIncident->title }}</h5>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $selectedIncident->description }}</p>
                        </div>
                    </div>

                    <!-- Photos transmises -->
                    @if ($selectedIncident->photos && count($selectedIncident->photos) > 0)
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Photos de l'incident</h4>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($selectedIncident->photos as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="rounded-lg overflow-hidden aspect-square border border-slate-200 dark:border-slate-800">
                                        <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rapport de Réparation / Travaux Agence -->
                    <div class="p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200/80 dark:border-indigo-900/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-900 dark:text-indigo-300">Rapport de Réparation de l'Agence</h4>
                            @if ($selectedIncident->repair_cost)
                                <span class="px-2.5 py-1 rounded-full bg-indigo-600 text-white font-bold text-xs">
                                    {{ number_format((float)$selectedIncident->repair_cost, 0, ',', ' ') }} FCFA
                                </span>
                            @endif
                        </div>

                        @if ($selectedIncident->repair_details)
                            <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                {{ $selectedIncident->repair_details }}
                            </p>
                        @else
                            <p class="text-xs text-slate-400 italic">Aucun détail d'intervention renseigné par l'agence pour le moment.</p>
                        @endif
                    </div>

                    <!-- Section Approbation / Rejet bailleur -->
                    @if ($selectedIncident->status?->value === 'resolved' || ! $selectedIncident->owner_confirmed_at)
                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="check" class="w-5 h-5 text-emerald-600" />
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Décision du Bailleur</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Montant Approuvé (FCFA)
                                    </label>
                                    <input type="number"
                                           wire:model="confirmedAmount"
                                           min="0"
                                           step="100"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                                    @error('confirmedAmount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Commentaire ou motif (optionnel si approuvé, obligatoire si rejeté)
                                    </label>
                                    <textarea wire:model="confirmNote"
                                              rows="3"
                                              placeholder="Ex: Accord pour le remplacement au montant convenu OR Motif de rejet..."
                                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none resize-none"></textarea>
                                    @error('confirmNote') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button wire:click="rejectRepair"
                                        class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                                    <x-icon name="x" class="w-4 h-4" />
                                    <span>Rejeter la réparation</span>
                                </button>

                                <button wire:click="saveConfirmRepair"
                                        class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                                    <x-icon name="check" class="w-4 h-4" />
                                    <span>Approuver la réparation</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Historique d'approbation / rejet -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                            <h4 class="font-bold text-slate-900 dark:text-white">Statut de la décision bailleur</h4>
                            @if ($selectedIncident->status?->value === 'rejected')
                                <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300">
                                    <p class="font-bold flex items-center gap-1.5"><x-icon name="x" class="w-4 h-4" /> Réparation rejetée le {{ $selectedIncident->owner_confirmed_at?->format('d/m/Y à H:i') }}</p>
                                    @if ($selectedIncident->owner_confirmation_note)
                                        <p class="mt-1 italic text-xs">« {{ $selectedIncident->owner_confirmation_note }} »</p>
                                    @endif
                                </div>
                            @elseif ($selectedIncident->owner_confirmed_at)
                                <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                                    <p class="font-bold flex items-center gap-1.5"><x-icon name="check" class="w-4 h-4" /> Approuvé le {{ $selectedIncident->owner_confirmed_at->format('d/m/Y à H:i') }}</p>
                                    <p class="mt-1 font-semibold">Montant approuvé: {{ number_format((float)$selectedIncident->owner_confirmed_amount, 0, ',', ' ') }} FCFA</p>
                                    @if ($selectedIncident->owner_confirmation_note)
                                        <p class="mt-1 italic text-xs">« {{ $selectedIncident->owner_confirmation_note }} »</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Footer -->
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex justify-end shrink-0">
                    <button wire:click="closeConfirmModal" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs hover:bg-slate-300 dark:hover:bg-slate-700 transition cursor-pointer">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
