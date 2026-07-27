<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('leases.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Retour</a>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Contrat {{ $lease->reference }}
                </h1>
                <x-badge :variant="$lease->status->badgeColor()">
                    {{ $lease->status->label() }}
                </x-badge>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Bien : <strong>{{ $lease->property?->title }}</strong> &mdash; Locataire : <strong>{{ $lease->tenant?->full_name }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($contractHtml)
                <a href="{{ route('leases.print', $lease->id) }}" target="_blank">
                    <x-button variant="secondary">🖨️ Imprimer / PDF</x-button>
                </a>
            @endif

            @if($lease->status->value === 'draft' || $lease->status->value === 'pending_signature')
                @can('leases.update')
                    <x-button wire:click="activate" wire:confirm="Activer ce contrat ? Le bien passera en 'Occupé' et les échéances seront créées.">
                        Activer le contrat
                    </x-button>
                @endcan
            @elseif($lease->status->value === 'active')
                @can('leases.update')
                    <x-button variant="secondary" class="text-red-600 border-red-200 hover:bg-red-50" wire:click="terminate" wire:confirm="Résilier ce contrat ?">
                        Résilier le contrat
                    </x-button>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Synthèse --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Parties prenantes</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Locataire :</strong> {{ $lease->tenant?->full_name }} ({{ $lease->tenant?->phone }})</p>
                <p><strong>Propriétaire :</strong> {{ $lease->property?->owner?->full_name }}</p>
                <p><strong>Bien :</strong> {{ $lease->property?->title }} ({{ $lease->property?->address }})</p>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Conditions financières</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Loyer HC :</strong> {{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} FCFA</p>
                <p><strong>Charges :</strong> {{ number_format((float)$lease->charges_amount, 0, ',', ' ') }} FCFA</p>
                <p><strong>Total Mensuel :</strong> <span class="font-bold text-primary-600 dark:text-primary-400">{{ number_format($lease->total_monthly_amount, 0, ',', ' ') }} FCFA</span></p>
                <p><strong>Caution :</strong> {{ number_format((float)$lease->deposit_amount, 0, ',', ' ') }} FCFA</p>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Dates & Échéance</h3>
            <div class="space-y-1 text-sm">
                <p><strong>Début du bail :</strong> {{ $lease->start_date?->format('d/m/Y') }}</p>
                <p><strong>Fin du bail :</strong> {{ $lease->end_date?->format('d/m/Y') }}</p>
                <p><strong>Jour de paiement :</strong> Le {{ $lease->payment_due_day }} du mois</p>
                @if($lease->signed_at)
                    <p><strong>Signé le :</strong> {{ $lease->signed_at->format('d/m/Y à H:i') }}</p>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Échéances générées --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Échéances de loyer ({{ $lease->rentSchedules->count() }})</h2>

        @if($lease->rentSchedules->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2">Période</th>
                            <th class="px-4 py-2">Date limite</th>
                            <th class="px-4 py-2">Montant attendu</th>
                            <th class="px-4 py-2">Montant payé</th>
                            <th class="px-4 py-2">Solde restant</th>
                            <th class="px-4 py-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($lease->rentSchedules as $sched)
                            <tr>
                                <td class="px-4 py-2 font-mono text-xs">{{ $sched->period }}</td>
                                <td class="px-4 py-2">{{ $sched->due_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 font-medium">{{ number_format((float)$sched->expected_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-2 text-green-600">{{ number_format((float)$sched->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ number_format((float)$sched->remaining_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-2">
                                    <x-badge :variant="$sched->status->badgeColor()">
                                        {{ $sched->status->label() }}
                                    </x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400">Aucune échéance générée. Activez le contrat pour créer automatiquement les échéances.</p>
        @endif
    </div>

    {{-- Aperçu du contrat HTML généré --}}
    @if($contractHtml)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Texte du contrat</h2>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm font-mono whitespace-pre-wrap leading-relaxed text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                {{ $contractHtml }}
            </div>
        </div>
    @endif
</div>
