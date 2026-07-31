<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('arrears.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Retour</a>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Dossier d'impayé &mdash; {{ $arrear->tenant?->full_name }}
                </h1>
                <x-badge :variant="$arrear->severity->badgeColor()">
                    {{ $arrear->severity->label() }}
                </x-badge>
                <x-badge :variant="$arrear->status->badgeColor()">
                    {{ $arrear->status->label() }}
                </x-badge>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Bien : <strong>{{ $arrear->lease?->property?->title }}</strong> &mdash; Échéance : <strong>{{ $arrear->rentSchedule?->period }}</strong>
            </p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Synthèse du dossier --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Locataire concerné</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Nom :</strong> {{ $arrear->tenant?->full_name }}</p>
                <p><strong>Email :</strong> {{ $arrear->tenant?->email ?? '—' }}</p>
                <p><strong>Téléphone :</strong> {{ $arrear->tenant?->phone ?? '—' }}</p>
                <p><strong>Urgence :</strong> {{ $arrear->tenant?->emergency_contact ?? '—' }}</p>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Situation financière</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Loyer attendu :</strong> {{ number_format((float)$arrear->amount_due, 0, ',', ' ') }} FCFA</p>
                <p><strong>Déjà versé :</strong> {{ number_format((float)$arrear->amount_paid, 0, ',', ' ') }} FCFA</p>
                <p><strong>Reste à payer :</strong> <span class="font-bold text-red-600">{{ number_format((float)$arrear->remaining_amount, 0, ',', ' ') }} FCFA</span></p>
                <p><strong>Premier retard :</strong> {{ $arrear->first_overdue_date?->format('d/m/Y') }}</p>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Statut du bail & bailleur</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Réf contrat :</strong> {{ $arrear->lease?->reference }}</p>
                <p><strong>Bailleur :</strong> {{ $arrear->lease?->property?->owner?->full_name }}</p>
                <p><strong>Tél bailleur :</strong> {{ $arrear->lease?->property?->owner?->phone ?? '—' }}</p>
            </div>
        </x-card>
    </div>

    {{-- Formulaire d'envoi d'une relance --}}
    @if($arrear->status->value === 'open')
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Transmettre une relance</h2>

            <form wire:submit="sendReminder" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-label for="channel">Canal d'envoi</x-label>
                        <select wire:model="channel" id="channel"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="email">E-mail</option>
                            <option value="sms">SMS</option>
                            <option value="in_app">In-App / Notification</option>
                        </select>
                        @error('channel') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-label for="customMessage">Message personnalisé (facultatif)</x-label>
                    <textarea wire:model="customMessage" id="customMessage" rows="3"
                              placeholder="Laissez vide pour utiliser le modèle automatique de relance..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    @error('customMessage') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-button type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>📩 Envoyer la relance</span>
                        <span wire:loading>Envoi en cours...</span>
                    </x-button>
                </div>
            </form>
        </div>
    @endif

    {{-- Historique des relances --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Historique des relances effectuées ({{ $arrear->reminders->count() }})</h2>

        @if($arrear->reminders->isNotEmpty())
            <div class="space-y-3">
                @foreach($arrear->reminders as $rem)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm space-y-1">
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Canal : <strong>{{ strtoupper($rem->channel) }}</strong></span>
                            <span>Envoyé le : {{ $rem->sent_at?->format('d/m/Y à H:i') }}</span>
                        </div>
                        <p class="text-gray-800 dark:text-gray-200">{{ $rem->content }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">Aucune relance transmise pour ce dossier.</p>
        @endif
    </div>
</div>
