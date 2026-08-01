<div>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-base-content">Mandat de Gestion {{ $contract->reference }}</h1>
                <span class="badge {{ $contract->status->badgeClass() }}">
                    {{ $contract->status->label() }}
                </span>
            </div>
            <p class="text-sm text-base-content/70">Propriétaire : <strong>{{ $contract->owner?->full_name }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('management-contracts.index') }}" class="btn btn-outline btn-sm">
                ← Retour
            </a>
            <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank" class="btn btn-primary btn-sm gap-2">
                🖨️ Imprimer / PDF
            </a>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success shadow-lg mb-6">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details sidebar -->
        <div class="space-y-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 space-y-4">
                    <h2 class="font-bold text-lg border-b pb-2">Résumé du Mandat</h2>

                    <div>
                        <div class="text-xs text-base-content/60">Propriétaire (Mandant)</div>
                        <div class="font-bold">{{ $contract->owner?->full_name }}</div>
                        <div class="text-xs text-base-content/70">{{ $contract->owner?->email }} | {{ $contract->owner?->phone }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-base-content/60">Commission Agence</div>
                        <div class="font-bold text-primary text-lg">{{ $contract->formatted_commission }}</div>
                        <div class="text-xs text-base-content/60 font-medium">Type : {{ $contract->commission_type === 'percentage' ? 'Pourcentage sur loyers' : 'Forfait fixe' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-base-content/60">Dates du mandat</div>
                        <div class="text-sm font-semibold">Prise d'effet : {{ $contract->start_date?->format('d/m/Y') }}</div>
                        <div class="text-xs text-base-content/60">Durée : {{ $contract->duration_months }} mois (Préavis : {{ $contract->notice_period_months }} mois)</div>
                    </div>

                    <div>
                        <div class="text-xs text-base-content/60">Conditions fiscales & Cautions</div>
                        <div class="text-xs space-y-1 mt-1">
                            <div>• IRF par le propriétaire : <span class="font-bold">{{ $contract->irf_paid_by_owner ? 'Oui' : 'Non' }}</span></div>
                            <div>• Caution conservée par agence : <span class="font-bold">{{ $contract->caution_kept_by_agency ? 'Oui' : 'Non' }}</span></div>
                        </div>
                    </div>

                    @if($contract->payment_bank_details)
                        <div>
                            <div class="text-xs text-base-content/60">Mode / Compte de versement</div>
                            <div class="text-xs font-mono bg-base-200 p-2 rounded mt-1">{{ $contract->payment_bank_details }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Associated Properties -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 space-y-3">
                    <h2 class="font-bold text-lg border-b pb-2">Bien(s) lié(s) à ce mandat</h2>

                    @forelse($contract->properties as $prop)
                        <div class="border-b last:border-0 pb-2">
                            <a href="{{ route('properties.edit', $prop->id) }}" class="font-bold text-sm text-primary hover:underline block">
                                {{ $prop->title }}
                            </a>
                            <div class="text-xs text-base-content/70">{{ $prop->reference }} - {{ $prop->address }}, {{ $prop->city }}</div>
                            <div class="text-xs font-semibold text-success mt-1">{{ number_format((float)$prop->rent_amount, 0, ',', ' ') }} FCFA / mois</div>
                        </div>
                    @empty
                        <div class="text-xs text-base-content/50 italic py-2">
                            Aucun bien n'est actuellement associé à ce mandat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Generated Contract Text Preview -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between border-b pb-3 mb-4">
                        <h2 class="font-bold text-lg">Aperçu du texte du Mandat</h2>
                        <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank" class="btn btn-xs btn-outline btn-primary">
                            Visualiser l'impression PDF
                        </a>
                    </div>
                    <div class="prose max-w-none whitespace-pre-wrap font-mono text-xs leading-relaxed bg-base-200/50 p-6 rounded-lg border border-base-300">
                        {{ $generatedText }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
