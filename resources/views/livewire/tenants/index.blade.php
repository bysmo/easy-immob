<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Locataires</h1>
                <x-badge color="indigo">{{ $tenants->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Base de données des locataires et occupants sous contrat.</p>
        </div>

        @can('tenants.create')
            <a href="{{ route('tenants.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nouveau locataire</span>
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

    <!-- Search Bar -->
    <x-card :padding="false" class="p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <x-input wire:model.live.debounce.300ms="search" 
                         type="search" 
                         icon="search" 
                         placeholder="Rechercher nom, email, tél, référence..." />
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
                        <th class="px-6 py-3.5">Locataire</th>
                        <th class="px-6 py-3.5">Coordonnées</th>
                        <th class="px-6 py-3.5">Contact d'Urgence</th>
                        <th class="px-6 py-3.5">Statut</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Reference -->
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $tenant->reference }}
                                </span>
                            </td>

                            <!-- Tenant Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs flex items-center justify-center border border-indigo-100 dark:border-indigo-900 shrink-0">
                                        {{ strtoupper(substr($tenant->first_name, 0, 1) . substr($tenant->last_name, 0, 1)) }}
                                    </div>
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $tenant->full_name }}
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800 dark:text-slate-200 font-semibold">{{ $tenant->email ?? '—' }}</div>
                                <div class="text-slate-400 mt-0.5">{{ $tenant->phone ?? '—' }}</div>
                            </td>

                            <!-- Emergency Contact -->
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $tenant->emergency_contact ?? '—' }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <x-badge :variant="$tenant->status === 'active' ? 'success' : 'muted'">
                                    {{ $tenant->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('tenants.update')
                                        <a href="{{ route('tenants.edit', $tenant->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                           title="Modifier">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                    @endcan

                                    @can('tenants.delete')
                                        <button wire:click="delete({{ $tenant->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir désactiver/supprimer ce locataire ?"
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                        <x-icon name="tenants" class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Aucun locataire trouvé</p>
                                    <p class="text-xs text-slate-400 mb-4">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                                    @can('tenants.create')
                                        <a href="{{ route('tenants.create') }}">
                                            <x-button size="sm" variant="secondary">
                                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                                <span>Ajouter un locataire</span>
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
        @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>
