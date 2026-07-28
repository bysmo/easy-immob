<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Messagerie & Échanges sur les biens</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Consultez et répondez aux demandes d'informations et négociations de baux.</p>
        </div>
        <a href="{{ route('catalog.index') }}">
            <x-button type="button" variant="primary">
                <x-icon name="building" class="w-4 h-4 mr-1.5" />
                <span>Rechercher un bien</span>
            </x-button>
        </a>
    </div>

    <!-- Filters -->
    <x-card class="!p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-input wire:model.live.debounce.300ms="search" type="search" icon="search" placeholder="Rechercher par bien, objet ou nom du locataire..." />
            </div>
            <div>
                <x-select wire:model.live="statusFilter">
                    <option value="all">Tous les statuts</option>
                    <option value="open">En cours (Ouvert)</option>
                    <option value="draft_lease_created">Brouillon de bail généré</option>
                    <option value="closed">Clôturé</option>
                </x-select>
            </div>
        </div>
    </x-card>

    <!-- Inquiries List Table / Cards -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($inquiries as $inquiry)
                <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        @php $photoList = $inquiry->property?->photo_list; @endphp
                        <img src="{{ $photoList[0] }}" class="w-16 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $inquiry->property?->title ?? 'Bien immobilier' }}
                                </h3>
                                <x-badge color="indigo" class="text-[10px]">{{ $inquiry->status }}</x-badge>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-1">
                                Interlocuteur : <strong>{{ $inquiry->user?->name ?? 'Locataire' }}</strong>
                                @if($inquiry->latestMessage)
                                    — {{ $inquiry->latestMessage->message }}
                                @endif
                            </p>
                            <span class="text-[10px] text-slate-400 font-mono mt-1 block">Dernier échange : {{ $inquiry->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <a href="{{ route('inquiries.chat', $inquiry->id) }}">
                            <x-button type="button" variant="secondary" size="sm">
                                <x-icon name="notifications" class="w-3.5 h-3.5 mr-1 text-emerald-600" />
                                <span>Ouvrir la chatbox</span>
                            </x-button>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-400 space-y-2">
                    <x-icon name="notifications" class="w-8 h-8 mx-auto opacity-50" />
                    <p class="text-xs font-semibold">Aucune discussion enregistrée pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div>
        {{ $inquiries->links() }}
    </div>
</div>
