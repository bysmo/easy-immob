<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Contrats de Location</h1>
                <x-badge color="indigo">{{ $leases->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Suivi des baux, signatures et gestion des statuts de location.</p>
        </div>

        @can('leases.create')
            <a href="{{ route('leases.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nouveau contrat</span>
                </x-button>
            </a>
        @endcan
    </div>

    <!-- Flash Notifications -->
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
    @if(session('error'))
        <div class="rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 p-4 text-sm text-rose-800 dark:text-rose-200 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                    <x-icon name="alert" class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <x-card :padding="false" class="p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <x-input wire:model.live.debounce.300ms="search" 
                         type="search" 
                         icon="search" 
                         placeholder="Rechercher par référence, bien, locataire..." />
            </div>

            <div class="w-full sm:w-60">
                <x-select wire:model.live="statusFilter" icon="filter">
                    <option value="">Tous les statuts</option>
                    @foreach($statusOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </x-select>
            </div>

            @if($search || $statusFilter)
                <button wire:click="$set('search', ''); $set('statusFilter', '')" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1 shrink-0">
                    <x-icon name="x" class="w-3.5 h-3.5" />
                    Réinitialiser les filtres
                </button>
            @endif
        </div>
    </x-card>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Référence</th>
                        <th class="px-6 py-3.5">Bien Immobilier</th>
                        <th class="px-6 py-3.5">Locataire Titulaire</th>
                        <th class="px-6 py-3.5">Période de Bail</th>
                        <th class="px-6 py-3.5">Total Mensuel</th>
                        <th class="px-6 py-3.5">Statut</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($leases as $lease)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Reference -->
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $lease->reference }}
                                </span>
                            </td>

                            <!-- Property -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $lease->property?->title ?? '—' }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $lease->property?->city }}
                                </div>
                            </td>

                            <!-- Tenant -->
                            <td class="px-6 py-4 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ $lease->tenant?->full_name ?? '—' }}
                            </td>

                            <!-- Dates -->
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                {{ $lease->start_date?->format('d/m/Y') }} &rarr; {{ $lease->end_date?->format('d/m/Y') }}
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                {{ number_format($lease->total_monthly_amount, 0, ',', ' ') }} FCFA
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <x-badge :variant="$lease->status->badgeColor()">
                                    {{ $lease->status->label() }}
                                </x-badge>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('leases.show', $lease->id) }}" 
                                       class="px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors">
                                        Consulter
                                    </a>

                                    @if($lease->status->value === 'draft' || $lease->status->value === 'pending_signature')
                                        @can('leases.update')
                                            <button wire:click="activate({{ $lease->id }})"
                                                    wire:confirm="Activer ce contrat ? Le bien passera à 'Occupé' et les échéances seront créées."
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900 transition-colors">
                                                Activer
                                            </button>
                                        @endcan
                                    @elseif($lease->status->value === 'active')
                                        @can('leases.update')
                                            <button wire:click="terminate({{ $lease->id }})"
                                                    wire:confirm="Résilier ce contrat ?"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 dark:hover:bg-rose-900 transition-colors">
                                                Résilier
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                        <x-icon name="leases" class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Aucun contrat trouvé</p>
                                    <p class="text-xs text-slate-400 mb-4">Aucun enregistrement ne correspond à vos filtres de recherche.</p>
                                    @can('leases.create')
                                        <a href="{{ route('leases.create') }}">
                                            <x-button size="sm" variant="secondary">
                                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                                <span>Créer un contrat</span>
                                            </x-button>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $leases->links() }}
            </div>
        @endif
    </div>
</div>
