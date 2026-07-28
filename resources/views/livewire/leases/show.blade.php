<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('leases.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour au suivi des baux</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Contrat {{ $lease->reference }}
                </h1>
                <x-badge :variant="$lease->status->badgeColor()">
                    {{ $lease->status->label() }}
                </x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Bien : <strong class="text-slate-700 dark:text-slate-200">{{ $lease->property?->title }}</strong> &bull; Locataire : <strong class="text-slate-700 dark:text-slate-200">{{ $lease->tenant?->full_name }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            @if($contractHtml)
                <a href="{{ route('leases.print', $lease->id) }}" target="_blank">
                    <x-button variant="secondary" size="sm">
                        <x-icon name="document" class="w-4 h-4" />
                        <span>Imprimer / PDF</span>
                    </x-button>
                </a>
            @endif

            @if($lease->status->value === 'draft' || $lease->status->value === 'pending_signature')
                @can('leases.update')
                    <x-button variant="primary" size="sm" type="button"
                              @click="$dispatch('open-confirm', {
                                  title: 'Activer le contrat de location',
                                  message: 'Voulez-vous vraiment activer ce contrat ? Le bien passera en status Occupé et les échéances de loyers seront créées.',
                                  confirmText: 'Activer le contrat',
                                  variant: 'success',
                                  onConfirm: () => $wire.activate()
                              })">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Activer le contrat</span>
                    </x-button>
                @endcan
            @elseif($lease->status->value === 'active')
                @can('leases.update')
                    <x-button variant="danger" size="sm" type="button"
                              @click="$dispatch('open-confirm', {
                                  title: 'Résilier le contrat de location',
                                  message: 'Êtes-vous sûr de vouloir résilier le contrat {{ $lease->reference }} ? Cette action est irréversible.',
                                  confirmText: 'Résilier le contrat',
                                  variant: 'danger',
                                  onConfirm: () => $wire.terminate()
                              })">
                        <x-icon name="alert" class="w-4 h-4" />
                        <span>Résilier le contrat</span>
                    </x-button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Flash Notifications -->
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

    <!-- Cards Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1 : Parties -->
        <x-card>
            <div class="flex items-center gap-2.5 mb-3 text-emerald-600 dark:text-emerald-400">
                <x-icon name="user" class="w-4 h-4" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Parties prenantes</h3>
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-400 block">Locataire Titulaire</span>
                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $lease->tenant?->full_name }}</span>
                    <span class="block text-slate-500">{{ $lease->tenant?->phone ?? 'Aucun téléphone' }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 block">Propriétaire Bailleur</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $lease->property?->owner?->full_name }}</span>
                </div>
            </div>
        </x-card>

        <!-- Card 2 : Conditions Financières -->
        <x-card>
            <div class="flex items-center gap-2.5 mb-3 text-sky-600 dark:text-sky-400">
                <x-icon name="wallet" class="w-4 h-4" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Finances du bail</h3>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Loyer HC :</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Charges mensuelles :</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ number_format((float)$lease->charges_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between pt-1 border-t border-slate-100 dark:border-slate-800 font-bold text-sm">
                    <span class="text-slate-700 dark:text-slate-300">Total Mensuel :</span>
                    <span class="text-emerald-600 dark:text-emerald-400">{{ number_format($lease->total_monthly_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-[11px] pt-1">
                    <span class="text-slate-400">Caution versée :</span>
                    <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format((float)$lease->deposit_amount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </x-card>

        <!-- Card 3 : Échéances & Calendrier -->
        <x-card>
            <div class="flex items-center gap-2.5 mb-3 text-indigo-600 dark:text-indigo-400">
                <x-icon name="reports" class="w-4 h-4" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Calendrier & Échéance</h3>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Prise d'effet :</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $lease->start_date?->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Fin du bail :</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $lease->end_date?->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jour d'échéance :</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">Le {{ $lease->payment_due_day }} du mois</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Rent Schedules Table Section -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Échéancier des Loyers ({{ $lease->rentSchedules->count() }})</h2>
                <p class="text-xs text-slate-500">Historique et statut de chaque échéance mensuelle.</p>
            </div>
        </div>

        @if($lease->rentSchedules->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Période</th>
                            <th class="px-4 py-3">Date Limite</th>
                            <th class="px-4 py-3">Montant Attendu</th>
                            <th class="px-4 py-3">Montant Payé</th>
                            <th class="px-4 py-3">Solde Restant</th>
                            <th class="px-4 py-3">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @foreach($lease->rentSchedules as $sched)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3 font-mono font-bold text-slate-700 dark:text-slate-300">{{ $sched->period }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $sched->due_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ number_format((float)$sched->expected_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-emerald-600 font-semibold">{{ number_format((float)$sched->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ number_format((float)$sched->remaining_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3">
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
            <div class="p-8 text-center bg-slate-50/50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                <x-icon name="leases" class="w-8 h-8 text-slate-400 mx-auto mb-2" />
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Aucune échéance générée</p>
                <p class="text-xs text-slate-400 mt-0.5">Activez le contrat de location pour déclencher la génération automatique des échéances.</p>
            </div>
        @endif
    </x-card>

    <!-- Contract Text Box -->
    @if($contractHtml)
        <x-card>
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-3">Texte Officiel du Contrat Généré</h2>
            <div class="p-5 bg-slate-50 dark:bg-slate-950 rounded-xl text-xs font-mono whitespace-pre-wrap leading-relaxed text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800">
                {{ $contractHtml }}
            </div>
        </x-card>
    @endif
</div>
