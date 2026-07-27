<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Centre de notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Historique et suivi des notifications générées par le système.</p>
        </div>
    </div>

    {{-- Liste des notifications --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($notifications as $notif)
                <div class="p-4 flex items-start justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $notif->subject }}</span>
                            <x-badge :variant="$notif->status === 'read' ? 'muted' : 'amber'">
                                {{ $notif->status === 'read' ? 'Lue' : 'Transmise' }}
                            </x-badge>
                            <span class="text-xs text-gray-400">({{ strtoupper($notif->channel) }})</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $notif->content }}</p>
                        <p class="text-xs text-gray-400">{{ $notif->created_at?->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($notif->status !== 'read')
                        <button wire:click="markAsRead({{ $notif->id }})"
                                class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                            Marquer comme lue
                        </button>
                    @endif
                </div>
            @empty
                <div class="p-10 text-center text-gray-400">
                    Aucune notification enregistrée.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
