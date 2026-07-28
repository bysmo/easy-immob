<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Recouvrement & Impayés</h1>
                <x-badge color="rose">{{ $arrears->total() }} dossiers</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Détection automatique et dossiers de relance des loyers en retard.</p>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 p-4 text-sm text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <x-icon name="check" class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher par locataire, bien..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="severityFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Toutes les sévérités</option>
                @foreach($severityOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les statuts</option>
                @foreach($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-datatable.controls>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th>Locataire & Bien</x-datatable.th>
                        <x-datatable.th>Période</x-datatable.th>
                        <x-datatable.th field="amount_due" :sortField="$sortField" :sortDirection="$sortDirection">Loyer Dû</x-datatable.th>
                        <x-datatable.th field="remaining_amount" :sortField="$sortField" :sortDirection="$sortDirection">Montant Restant</x-datatable.th>
                        <x-datatable.th field="first_overdue_date" :sortField="$sortField" :sortDirection="$sortDirection">Retard depuis le</x-datatable.th>
                        <x-datatable.th field="severity" :sortField="$sortField" :sortDirection="$sortDirection">Sévérité</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($arrears as $arrear)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $arrear->tenant?->full_name ?? 'Inconnu' }}
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    {{ $arrear->lease?->property?->title ?? 'Bien inconnu' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                {{ $arrear->rentSchedule?->period }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format((float)$arrear->amount_due, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-rose-600 dark:text-rose-400">
                                {{ number_format((float)$arrear->remaining_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $arrear->first_overdue_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$arrear->severity->badgeColor()">
                                    {{ $arrear->severity->label() }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$arrear->status->badgeColor()">
                                    {{ $arrear->status->label() }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('arrears.show', $arrear->id) }}" 
                                       class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-bold transition">
                                        Consulter
                                    </a>
                                    @if($arrear->status->value === 'open')
                                        @can('arrears.manage')
                                            <button type="button"
                                                    @click="$dispatch('open-confirm', {
                                                        title: 'Envoyer une relance pour impayé',
                                                        message: 'Voulez-vous envoyer une notification de relance e-mail au locataire pour le dossier {{ $arrear->reference }} ?',
                                                        confirmText: 'Envoyer la relance',
                                                        variant: 'warning',
                                                        onConfirm: () => $wire.sendReminder({{ $arrear->id }})
                                                    })"
                                                    class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition cursor-pointer">
                                                Relancer
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                Aucun dossier d'impayé en cours.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($arrears->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $arrears->links() }}
            </div>
        @endif
    </div>
</div>
