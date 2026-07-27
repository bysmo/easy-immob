<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Centre de Notifications</h1>
            <x-badge color="indigo">{{ $notifications->total() }} au total</x-badge>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Alertes, relances d'impayés et évènements système de votre agence.</p>
    </div>

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher une alerte..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="typeFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les types</option>
                <option value="arrear">Relances & Impayés</option>
                <option value="lease">Baux locatifs</option>
                <option value="system">Système</option>
            </select>
        </x-slot:filters>
    </x-datatable.controls>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="title" :sortField="$sortField" :sortDirection="$sortDirection">Sujet / Notification</x-datatable.th>
                        <x-datatable.th field="type" :sortField="$sortField" :sortDirection="$sortDirection">Type</x-datatable.th>
                        <x-datatable.th field="created_at" :sortField="$sortField" :sortDirection="$sortDirection">Date & Heure</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $notification->status === 'unread' ? 'bg-emerald-50/30 dark:bg-emerald-950/20 font-bold' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $notification->title }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 font-normal mt-0.5">
                                    {{ $notification->message }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge color="indigo">{{ ucfirst($notification->type ?? 'Général') }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $notification->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$notification->status === 'read' ? 'muted' : 'warning'">
                                    {{ $notification->status === 'read' ? 'Lu' : 'Non lu' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($notification->status === 'unread')
                                    <button wire:click="markAsRead({{ $notification->id }})" 
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-100 hover:text-emerald-700 text-xs font-bold transition">
                                        Marquer comme lu
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucune notification disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
