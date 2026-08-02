<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mandats de Gestion</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Vos contrats de mandat avec chaque agence</p>
    </div>

    @if ($contracts->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800">
            <x-icon name="leases" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
            <p class="text-slate-500">Aucun mandat de gestion enregistré.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($contracts as $contract)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center">
                                <x-icon name="building" class="w-5 h-5 text-emerald-700 dark:text-emerald-300" />
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $contract->agency?->name ?? 'Agence inconnue' }}</p>
                                <p class="text-xs text-slate-500 font-mono">Réf. {{ $contract->reference }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @php
                                $statusClasses = [
                                    'draft'      => 'bg-slate-100 text-slate-600 border-slate-200',
                                    'active'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'expired'    => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'terminated' => 'bg-rose-100 text-rose-700 border-rose-200',
                                ];
                                $st = $contract->status instanceof \BackedEnum ? $contract->status->value : (string)$contract->status;
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $statusClasses[$st] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ $contract->status?->label() }}
                            </span>

                            @if ($st === 'active')
                                <button wire:click="openRevokeModal({{ $contract->id }})"
                                        class="text-xs px-3 py-1.5 border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold rounded-xl transition flex items-center gap-1.5">
                                    <x-icon name="close" class="w-3.5 h-3.5" />
                                    Résilier
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Détails --}}
                    <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Date de début</p>
                            <p class="font-medium text-slate-900 dark:text-white">
                                {{ $contract->start_date ? $contract->start_date->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Date de fin</p>
                            <p class="font-medium text-slate-900 dark:text-white">
                                {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indéterminée' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Taux commission</p>
                            <p class="font-bold text-amber-700 dark:text-amber-400">
                                {{ $contract->commission_rate ? $contract->commission_rate . ' %' : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Nombre de biens</p>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $contract->properties->count() }} bien(s)</p>
                        </div>
                    </div>

                    @if ($contract->properties->count() > 0)
                        <div class="px-6 pb-5 flex flex-wrap gap-2">
                            @foreach ($contract->properties as $property)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    {{ $property->title }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal Résiliation --}}
    @if ($showRevokeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-md w-full p-6 space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950 flex items-center justify-center">
                        <x-icon name="close" class="w-5 h-5 text-rose-700 dark:text-rose-300" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Résilier le mandat</h3>
                        <p class="text-xs text-slate-500">Réf. {{ $revokeContractRef }}</p>
                    </div>
                </div>

                <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-xl p-4">
                    <p class="text-sm text-rose-700 dark:text-rose-300">
                        ⚠️ Cette action est <strong>immédiate et irréversible</strong>. Le mandat sera résilié et l'agence en sera notifiée.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showRevokeModal', false)"
                            class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-sm transition">
                        Annuler
                    </button>
                    <button wire:click="revokeContract"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm transition">
                        <span wire:loading.remove>Confirmer la résiliation</span>
                        <span wire:loading>Résiliation…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
