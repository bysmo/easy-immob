<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('management-contracts.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour aux mandats de gestion</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Mandat {{ $contract->reference }}</h1>
                <x-badge :variant="$contract->status?->badgeColor() ?? 'muted'">
                    {{ $contract->status?->label() ?? '—' }}
                </x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mandant / Propriétaire : <strong>{{ $contract->owner?->full_name }}</strong></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="printer" class="w-4 h-4" />
                    <span>Imprimer / PDF</span>
                </x-button>
            </a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Info Cards -->
        <div class="space-y-6">
            <!-- Synthèse du Mandat -->
            <x-card>
                <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <x-icon name="document" class="w-4 h-4" />
                    </div>
                    <h2 class="font-bold text-sm text-slate-900 dark:text-white">Résumé du Mandat</h2>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Propriétaire (Mandant)</span>
                        <a href="{{ route('owners.edit', $contract->owner_id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors">
                            {{ $contract->owner?->full_name }}
                        </a>
                        <div class="text-slate-400 mt-0.5">{{ $contract->owner?->phone ?? $contract->owner?->email }}</div>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Commission Agence</span>
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 text-base mt-0.5">
                            {{ $contract->formatted_commission }}
                        </div>
                        <div class="text-slate-400 text-[11px]">
                            {{ $contract->commission_type === 'percentage' ? 'Pourcentage sur les loyers bruts' : 'Forfait mensuel fixe' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Prise d'effet & Durée</span>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                            {{ $contract->start_date?->format('d/m/Y') }}
                        </div>
                        <div class="text-slate-400">Durée : {{ $contract->duration_months }} mois (Préavis : {{ $contract->notice_period_months }} mois)</div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                            <span>IRF par le propriétaire :</span>
                            <span class="font-bold">{{ $contract->irf_paid_by_owner ? 'Oui' : 'Non' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                            <span>Caution gérée par agence :</span>
                            <span class="font-bold">{{ $contract->caution_kept_by_agency ? 'Oui' : 'Non' }}</span>
                        </div>
                    </div>

                    @if($contract->payment_bank_details)
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block font-medium mb-1">RIB / Compte de reversement</span>
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 font-mono text-[11px] text-slate-700 dark:text-slate-300">
                                {{ $contract->payment_bank_details }}
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Biens rattachés -->
            <x-card>
                <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <x-icon name="building" class="w-4 h-4" />
                    </div>
                    <h2 class="font-bold text-sm text-slate-900 dark:text-white">Bien(s) géré(s) ({{ $contract->properties->count() }})</h2>
                </div>

                <div class="space-y-3">
                    @forelse($contract->properties as $prop)
                        <div class="p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                            <a href="{{ route('properties.edit', $prop->id) }}" class="font-bold text-xs text-slate-900 dark:text-white hover:text-emerald-600 transition-colors block">
                                {{ $prop->title }}
                            </a>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $prop->reference }} — {{ $prop->address }}, {{ $prop->city }}</div>
                            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format((float)$prop->rent_amount, 0, ',', ' ') }} FCFA / mois</div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 italic py-2 text-center">
                            Aucun bien n'est actuellement associé à ce mandat.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Generated Contract Text Preview -->
        <div class="lg:col-span-2">
            <x-card>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="font-bold text-base text-slate-900 dark:text-white">Aperçu du Mandat de Gestion</h2>
                        <p class="text-xs text-slate-500">Texte officiel prêt pour la signature ou l'impression PDF.</p>
                    </div>
                    <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank">
                        <x-button variant="secondary" size="sm" class="text-xs">
                            <x-icon name="printer" class="w-3.5 h-3.5" />
                            <span>Imprimer PDF</span>
                        </x-button>
                    </a>
                </div>

                <div class="whitespace-pre-wrap font-mono text-xs leading-relaxed bg-slate-50 dark:bg-slate-950/60 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-slate-800 dark:text-slate-200">
                    {{ $generatedText }}
                </div>
            </x-card>
        </div>
    </div>
</div>
