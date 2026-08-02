<div class="space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Mandats de Gestion Immobilière</h1>
                <x-badge color="emerald">{{ $contracts->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gérez les contrats de mandat confiés par vos propriétaires à l'agence.</p>
        </div>

        <a href="{{ route('management-contracts.create') }}">
            <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Nouveau Mandat de Gestion</span>
            </x-button>
        </a>
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
    <x-datatable.controls placeholder="Rechercher par référence, titre, propriétaire..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les statuts</option>
                @foreach($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>

            <select wire:model.live="ownerFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les propriétaires</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
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
                        <x-datatable.th field="reference" :sortField="$sortField" :sortDirection="$sortDirection">Référence</x-datatable.th>
                        <x-datatable.th field="owner_id" :sortField="$sortField" :sortDirection="$sortDirection">Propriétaire (Mandant)</x-datatable.th>
                        <x-datatable.th field="commission_value" :sortField="$sortField" :sortDirection="$sortDirection">Commission Agence</x-datatable.th>
                        <x-datatable.th>Biens gérés</x-datatable.th>
                        <x-datatable.th field="start_date" :sortField="$sortField" :sortDirection="$sortDirection">Période d'effet</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($contracts as $contract)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <a href="{{ route('management-contracts.show', $contract->id) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                    {{ $contract->reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $contract->owner?->full_name ?? '—' }}
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    {{ $contract->owner?->phone ?? $contract->owner?->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ $contract->formatted_commission }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($contract->properties->count() > 0)
                                    <div class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $contract->properties->count() }} bien(s) lié(s)
                                    </div>
                                    <div class="text-slate-400 text-[11px] truncate max-w-xs mt-0.5">
                                        {{ $contract->properties->pluck('title')->implode(', ') }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Aucun bien rattaché</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800 dark:text-slate-200 font-semibold">
                                    Début : {{ $contract->start_date?->format('d/m/Y') }}
                                </div>
                                <div class="text-slate-400 mt-0.5">
                                    Durée : {{ $contract->duration_months }} mois
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$contract->status?->badgeColor() ?? 'muted'">
                                    {{ $contract->status?->label() ?? '—' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('management-contracts.show', $contract->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                       title="Consulter">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>

                                    <a href="{{ route('management-contracts.print', $contract->id) }}" 
                                       target="_blank"
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                       title="Imprimer le Mandat PDF">
                                        <x-icon name="printer" class="w-4 h-4" />
                                    </a>

                                    @if($contract->status === \App\Domain\Owner\Enums\ManagementContractStatus::Active)
                                        <span class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 opacity-50 cursor-not-allowed" 
                                              title="Un mandat de gestion actif ne peut pas être supprimé par l'agence">
                                            <x-icon name="trash" class="w-4 h-4 text-slate-300 dark:text-slate-600" />
                                        </span>
                                    @else
                                        <button type="button"
                                                @click="$dispatch('open-confirm', {
                                                    title: 'Supprimer le mandat de gestion',
                                                    message: 'Êtes-vous sûr de vouloir supprimer le mandat {{ $contract->reference }} ? Cette action est irréversible.',
                                                    confirmText: 'Supprimer le mandat',
                                                    variant: 'danger',
                                                    onConfirm: () => $wire.delete({{ $contract->id }})
                                                })"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                title="Supprimer">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Aucun mandat de gestion trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>
