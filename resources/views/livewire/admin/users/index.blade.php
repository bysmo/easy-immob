<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des utilisateurs</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Utilisateurs rattachés à votre agence.</p>
        </div>
        @can('users.create')
            <a href="{{ route('admin.users.create') }}">
                <x-button>+ Nouvel utilisateur</x-button>
            </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Barre de recherche --}}
    <div class="max-w-sm">
        <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher un utilisateur…" />
    </div>

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 text-left">Nom</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Rôle</th>
                    <th class="px-5 py-3 text-left">Créé le</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @if($user->roles->isNotEmpty())
                                <x-badge>{{ $user->roles->first()->name }}</x-badge>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('users.update')
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                    Modifier
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    @endif
</div>
