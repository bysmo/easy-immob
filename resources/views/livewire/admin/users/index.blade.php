<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Utilisateurs & Équipe</h1>
                <x-badge color="indigo">{{ $users->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gérez les membres de votre agence et attribuez des rôles d'accès.</p>
        </div>

        @can('users.create')
            <a href="{{ route('admin.users.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nouvel utilisateur</span>
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
                         placeholder="Rechercher par nom, email..." />
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
                        <th class="px-6 py-3.5">Nom & Utilisateur</th>
                        <th class="px-6 py-3.5">Adresse Email</th>
                        <th class="px-6 py-3.5">Rôle Attribué</th>
                        <th class="px-6 py-3.5">Date de création</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- User Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center border border-emerald-200 dark:border-emerald-800 shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $user->email }}
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-4">
                                @if($user->roles->isNotEmpty())
                                    <x-badge color="indigo">{{ $user->roles->first()->name }}</x-badge>
                                @else
                                    <span class="text-slate-400 text-xs">— Aucun —</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                @can('users.update')
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="inline-flex items-center gap-1 p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors text-xs font-semibold"
                                       title="Modifier">
                                        <x-icon name="edit" class="w-4 h-4" />
                                        <span>Modifier</span>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                        <x-icon name="users" class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Aucun utilisateur trouvé</p>
                                    <p class="text-xs text-slate-400 mb-4">Aucun résultat pour cette recherche.</p>
                                    @can('users.create')
                                        <a href="{{ route('admin.users.create') }}">
                                            <x-button size="sm" variant="secondary">
                                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                                <span>Ajouter un utilisateur</span>
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
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
