<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Propriétaires</h1>
                <x-badge color="indigo">{{ $owners->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gérez l'ensemble des bailleurs et mandataires enregistrés dans l'agence.</p>
        </div>

        @can('owners.create')
            <a href="{{ route('owners.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nouveau propriétaire</span>
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

    <!-- Search & Filter Controls -->
    <x-card :padding="false" class="p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <x-input wire:model.live.debounce.300ms="search" 
                         type="search" 
                         icon="search" 
                         placeholder="Rechercher par nom, email, réf..." />
            </div>

            @if($search)
                <button wire:click="$set('search', '')" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1">
                    <x-icon name="x" class="w-3.5 h-3.5" />
                    Réinitialiser la recherche
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
                        <th class="px-6 py-3.5">Propriétaire</th>
                        <th class="px-6 py-3.5">Coordonnées</th>
                        <th class="px-6 py-3.5">Statut</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Reference -->
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $owner->reference }}
                                </span>
                            </td>

                            <!-- Owner Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 shrink-0">
                                        {{ strtoupper(substr($owner->first_name, 0, 1) . substr($owner->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $owner->full_name }}
                                        </div>
                                        @if($owner->company_name)
                                            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                                <x-icon name="building" class="w-3 h-3" />
                                                <span>{{ $owner->company_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800 dark:text-slate-200 font-semibold">{{ $owner->email ?? '—' }}</div>
                                <div class="text-slate-400 mt-0.5">{{ $owner->phone ?? '—' }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <x-badge :variant="$owner->status === 'active' ? 'success' : 'muted'">
                                    {{ $owner->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('owners.update')
                                        <a href="{{ route('owners.edit', $owner->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                           title="Modifier">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                    @endcan

                                    @can('owners.delete')
                                        <button wire:click="delete({{ $owner->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir désactiver/supprimer ce propriétaire ?"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                                title="Supprimer">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                        <x-icon name="owners" class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Aucun propriétaire trouvé</p>
                                    <p class="text-xs text-slate-400 mb-4">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                                    @can('owners.create')
                                        <a href="{{ route('owners.create') }}">
                                            <x-button size="sm" variant="secondary">
                                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                                <span>Ajouter un propriétaire</span>
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
        @if($owners->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $owners->links() }}
            </div>
        @endif
    </div>
</div>
