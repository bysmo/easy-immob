<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb & Property Context -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('inquiries.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des discussions</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Discussion — {{ $inquiry->property->title }}</h1>
                <x-badge color="indigo" class="text-xs">{{ $inquiry->status }}</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                <span>Interlocuteur : <strong>{{ $inquiry->user?->name ?? 'Locataire' }}</strong></span>
                <span>• Bien : {{ $inquiry->property->city }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('catalog.show', $inquiry->property_id) }}" target="_blank">
                <x-button type="button" variant="secondary">Voir la fiche du bien</x-button>
            </a>
            <x-button type="button" variant="primary" wire:click="openDraftLeaseModal">
                <x-icon name="check" class="w-4 h-4 mr-1.5" />
                <span>Conclure un brouillon de bail</span>
            </x-button>
        </div>
    </div>

    <!-- Chat Box -->
    <x-card class="!p-0 flex flex-col h-[550px] overflow-hidden border-slate-200 dark:border-slate-800">

        <!-- Chat Header -->
        <div class="p-4 bg-slate-50 dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php $photoList = $inquiry->property->photo_list; @endphp
                <img src="{{ $photoList[0] }}" class="w-12 h-9 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $inquiry->property->title }}</h3>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                        {{ number_format((float)$inquiry->property->rent_amount, 0, ',', ' ') }} FCFA/mois
                    </span>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                Agence : {{ $inquiry->agency?->name }}
            </span>
        </div>

        <!-- Chat Messages Thread -->
        <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50/40 dark:bg-slate-950/40 scrollbar-thin">
            @forelse($inquiry->messages as $msg)
                @php
                    $isSelf = $msg->user_id === auth()->id();
                @endphp
                <div class="flex flex-col {{ $isSelf ? 'items-end' : 'items-start' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                            {{ $msg->user?->name ?? ($msg->is_agency ? 'Agence' : 'Locataire') }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $msg->created_at->format('H:i, d/m') }}</span>
                    </div>

                    <div class="max-w-md p-3.5 rounded-2xl text-xs shadow-2xs leading-relaxed {{ $isSelf ? 'bg-emerald-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-tl-none' }}">
                        {{ $msg->message }}
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center text-center text-slate-400 text-xs">
                    Aucun message échangé pour le moment. Écrivez ci-dessous pour démarrer.
                </div>
            @endforelse
        </div>

        <!-- Chat Input Form -->
        <form wire:submit.prevent="sendMessage" class="p-3 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center gap-2">
            <x-input wire:model="messageText" placeholder="Tapez votre message ici..." class="flex-1" autofocus />
            <x-button type="submit" variant="primary" class="shrink-0">
                <span>Envoyer</span>
                <x-icon name="arrow-right" class="w-4 h-4 ml-1" />
            </x-button>
        </form>

    </x-card>

    <!-- Modal Brouillon de Bail -->
    @if($showDraftLeaseModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-xl rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Conclure un brouillon de contrat de bail</h3>
                            <p class="text-xs text-slate-500">Générer la proposition de bail pour : {{ $inquiry->property->title }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showDraftLeaseModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label for="selected_tenant_id" :required="true">Locataire signataire</x-label>
                        <x-select wire:model="selected_tenant_id" id="selected_tenant_id" icon="tenants">
                            <option value="">— Sélectionner le locataire —</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->reference }}) — {{ $t->phone }}</option>
                            @endforeach
                        </x-select>
                        @error('selected_tenant_id') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="start_date" :required="true">Date de prise d'effet</x-label>
                            <x-input wire:model="start_date" type="date" id="start_date" :error="$errors->first('start_date')" />
                        </div>

                        <div>
                            <x-label for="duration_months" :required="true">Durée du bail (mois)</x-label>
                            <x-input wire:model="duration_months" type="number" min="1" id="duration_months" placeholder="12" :error="$errors->first('duration_months')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="rent_amount" :required="true">Loyer mensuel (FCFA)</x-label>
                            <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" icon="wallet" :error="$errors->first('rent_amount')" />
                        </div>

                        <div>
                            <x-label for="deposit_amount" :required="true">Dépôt de garantie / Caution (FCFA)</x-label>
                            <x-input wire:model="deposit_amount" type="number" step="1000" id="deposit_amount" icon="wallet" :error="$errors->first('deposit_amount')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-button type="button" variant="secondary" wire:click="$set('showDraftLeaseModal', false)">Annuler</x-button>
                    <x-button type="button" variant="primary" wire:click="createDraftLease">Générer le brouillon de bail</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
