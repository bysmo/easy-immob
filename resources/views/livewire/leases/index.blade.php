<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Contrats de Location</h1>
                <x-badge color="teal">{{ $leases->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Baux locatifs en cours, brouillons et contrats résiliés.</p>
        </div>

        @can('leases.create')
            <a href="{{ route('leases.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Créer un contrat</span>
                </x-button>
            </a>
        @endcan
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
    <x-datatable.controls placeholder="Rechercher par référence, bien, locataire..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
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
                        <x-datatable.th field="reference" :sortField="$sortField" :sortDirection="$sortDirection">Référence</x-datatable.th>
                        <x-datatable.th>Bien & Locataire</x-datatable.th>
                        <x-datatable.th field="start_date" :sortField="$sortField" :sortDirection="$sortDirection">Période du bail</x-datatable.th>
                        <x-datatable.th field="rent_amount" :sortField="$sortField" :sortDirection="$sortDirection">Loyer mensuel</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($leases as $lease)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $lease->reference }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $lease->property?->title ?? 'Bien inconnu' }}
                                </div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                    Locataire: {{ $lease->tenant?->full_name ?? 'Inconnu' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                <div>Du {{ $lease->start_date?->format('d/m/Y') }}</div>
                                <div class="text-slate-400">Au {{ $lease->end_date ? $lease->end_date->format('d/m/Y') : 'Indéterminée' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$lease->status?->badgeColor() ?? 'muted'">
                                    {{ $lease->status?->label() ?? '—' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('leases.show', $lease->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                       title="Détails">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>

                                    @if($lease->status?->value === 'draft')
                                        <button wire:click="activate({{ $lease->id }})" 
                                                wire:confirm="Activer ce contrat de location ?"
                                                class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold hover:bg-emerald-200 transition">
                                            Activer
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Aucun contrat de location trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $leases->links() }}
            </div>
        @endif
    </div>
</div>
