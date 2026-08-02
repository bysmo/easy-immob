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
                                    @if ($incident->status?->value === 'resolved' && ! $incident->owner_confirmed_at)
                                        <button wire:click="confirmRepair({{ $incident->id }})"
                                                class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition">
                                            Approuver
                                        </button>
                                    @elseif ($incident->owner_confirmed_at)
                                        <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1 justify-center">
                                            <x-icon name="check" class="w-3.5 h-3.5" /> Approuvé
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal d'approbation --}}
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-md w-full p-6 space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 text-emerald-700 dark:text-emerald-300" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Approuver la réparation</h3>
                        <p class="text-xs text-slate-500">Confirmez le coût de la réparation</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Montant approuvé (FCFA)
                        </label>
                        <input type="number"
                               wire:model="confirmedAmount"
                               min="0"
                               step="100"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                        @error('confirmedAmount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Commentaire (optionnel)
                        </label>
                        <textarea wire:model="confirmNote"
                                  rows="3"
                                  placeholder="Ex: Accord pour le remplacement du climatiseur au tarif convenu…"
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showConfirmModal', false)"
                            class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-sm transition">
                        Annuler
                    </button>
                    <button wire:click="saveConfirmRepair"
                            class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
