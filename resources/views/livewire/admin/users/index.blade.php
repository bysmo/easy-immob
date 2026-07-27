<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Gestion des Utilisateurs</h1>
                <x-badge color="indigo">{{ $users->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Comptes utilisateurs et affectation des rôles de l'agence.</p>
        </div>

        @can('users.create')
            <a href="{{ route('admin.users.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Créer un utilisateur</span>
                </x-button>
            </a>
        @endcan
    </div>

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher par nom, email..." :perPage="$perPage" :search="$search" />

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="name" :sortField="$sortField" :sortDirection="$sortDirection">Utilisateur</x-datatable.th>
                        <x-datatable.th field="email" :sortField="$sortField" :sortDirection="$sortDirection">Email</x-datatable.th>
                        <x-datatable.th>Rôle</x-datatable.th>
                        <x-datatable.th field="created_at" :sortField="$sortField" :sortDirection="$sortDirection">Créé le</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-300">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @forelse($user->roles as $role)
                                    <x-badge color="indigo">{{ $role->name }}</x-badge>
                                @empty
                                    <x-badge color="gray">Aucun rôle</x-badge>
                                @endforelse
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $user->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @can('users.update')
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                       title="Modifier">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
