<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture de Reversement Bailleur - {{ $payout->reference }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 40px; color: #1e293b; line-height: 1.5; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #059669; padding-bottom: 15px; margin-bottom: 25px; }
        .agency-name { font-size: 22px; font-weight: bold; color: #059669; }
        .doc-title { font-size: 18px; font-weight: bold; text-align: right; text-transform: uppercase; color: #334155; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 25px; gap: 20px; }
        .box { flex: 1; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; background: #f8fafc; font-size: 13px; }
        .box-title { font-weight: bold; text-transform: uppercase; font-size: 11px; color: #64748b; margin-bottom: 8px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }
        .table th { background: #f1f5f9; text-transform: uppercase; font-size: 10px; color: #475569; }
        .summary-wrapper { display: flex; justify-content: flex-end; margin-top: 25px; }
        .summary-box { width: 320px; border: 2px solid #059669; padding: 15px; border-radius: 8px; font-size: 13px; background: #f0fdf4; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .summary-total { font-weight: bold; font-size: 15px; color: #059669; border-top: 1px solid #cbd5e1; pt-2; margin-top: 8px; }
        .settlement-history { margin-top: 30px; font-size: 12px; }
        .settlement-history h4 { font-size: 12px; text-transform: uppercase; color: #475569; margin-bottom: 8px; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; font-size: 12px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
            🖨️ Imprimer ou Enregistrer en PDF
        </button>
    </div>

    @php $agency = $payout->agency; @endphp
    <div class="header">
        <div>
            @if($agency?->logo_url)
                <img src="{{ $agency->logo_url }}" alt="Logo {{ $agency->name }}" style="max-height: 60px; max-width: 220px; margin-bottom: 8px;">
            @endif
            <div class="agency-name">{{ $agency?->name ?? 'EasyImmob' }}</div>
            @if($agency?->legal_name)
                <div style="font-size: 11px; color: #64748b; font-style: italic;">{{ $agency->legal_name }}</div>
            @endif
            <div style="font-size: 12px; color: #64748b;">{{ $agency?->address ?? 'Gestion Locative Professionnelle' }}</div>
            <div style="font-size: 12px; color: #64748b;">
                @if($agency?->phone) Tel: {{ $agency->phone }} @endif
                @if($agency?->email) | Email: {{ $agency->email }} @endif
            </div>
            @if($agency?->nif_rccm)
                <div style="font-size: 11px; color: #64748b;">N° Immat. / NIF : {{ $agency->nif_rccm }}</div>
            @endif
            @if($agency?->is_subject_to_tva)
                <div style="font-size: 10px; color: #059669; font-weight: bold; margin-top: 2px;">Assujetti à la TVA ({{ $agency->tva_rate }}%)</div>
            @endif
        </div>
        <div>
            <div class="doc-title">Décompte de Reversement</div>
            <div style="font-size: 13px; font-weight: bold; color: #059669; text-align: right;">Ref: {{ $payout->reference }}</div>
            <div style="font-size: 12px; color: #64748b; text-align: right;">Date : {{ $payout->created_at?->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="box">
            <div class="box-title">Bailleur / Beneficiaire</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 4px;">{{ $payout->owner?->full_name }}</div>
            <div>Ref Bailleur : <strong>{{ $payout->owner?->reference }}</strong></div>
            <div>Adresse : {{ $payout->owner?->address ?? '—' }}</div>
            <div>Contact : {{ $payout->owner?->phone }} / {{ $payout->owner?->email }}</div>
        </div>

        <div class="box">
            <div class="box-title">Details de la Période</div>
            <div>Période calculée : <strong>{{ $payout->period }}</strong></div>
            <div>Mode de calcul : <strong>{{ $payout->calculation_type->label() }}</strong></div>
            <div>Statut : <strong>{{ $payout->status->label() }}</strong></div>
        </div>
    </div>

    <h4 style="font-size: 12px; text-transform: uppercase; color: #475569; margin-bottom: 8px;">Détail des Biens & Loyers Inclus</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Bien Immobiliers</th>
                <th style="text-align: right;">Loyer Brut</th>
                <th style="text-align: right;">Commission Agence</th>
                <th style="text-align: right;">IRF / Taxes</th>
                <th style="text-align: right;">Montant Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payout->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->property?->title }}</strong>
                        <div style="font-size: 10px; color: #64748b;">{{ $item->description }}</div>
                    </td>
                    <td style="text-align: right;">{{ number_format($item->gross_amount, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ number_format($item->commission_amount, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ number_format($item->irf_amount, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item->net_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-wrapper">
        <div class="summary-box">
            <div class="summary-row">
                <span>Total Loyers Brut :</span>
                <strong>{{ number_format($payout->gross_amount, 0, ',', ' ') }} FCFA</strong>
            </div>
            <div class="summary-row" style="color: #b45309;">
                <span>Commission Agence ({{ $payout->commission_rate }}%) :</span>
                <strong>- {{ number_format($payout->commission_amount, 0, ',', ' ') }} FCFA</strong>
            </div>
            @if($payout->irf_amount > 0)
                <div class="summary-row" style="color: #b45309;">
                    <span>Impôt Foncier (IRF) :</span>
                    <strong>- {{ number_format($payout->irf_amount, 0, ',', ' ') }} FCFA</strong>
                </div>
            @endif
            <div class="summary-row summary-total">
                <span>NET À REVERSER :</span>
                <span>{{ number_format($payout->net_amount, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="summary-row" style="margin-top: 8px; color: #047857;">
                <span>Total Déjà Réglé :</span>
                <strong>{{ number_format($payout->paid_amount, 0, ',', ' ') }} FCFA</strong>
            </div>
            <div class="summary-row" style="font-weight: bold; color: #1e293b;">
                <span>Solde Restant :</span>
                <strong>{{ number_format($payout->remaining_amount, 0, ',', ' ') }} FCFA</strong>
            </div>
        </div>
    </div>

    @if($payout->settlements->count() > 0)
        <div class="settlement-history">
            <h4>Historique des Règlements Effectués</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date de paiement</th>
                        <th>Moyen de règlement</th>
                        <th>Référence transaction</th>
                        <th style="text-align: right;">Montant réglé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payout->settlements as $st)
                        <tr>
                            <td><strong>{{ $st->reference }}</strong></td>
                            <td>{{ $st->payment_date?->format('d/m/Y') }}</td>
                            <td>{{ $st->payment_method?->label() }}</td>
                            <td>{{ $st->transaction_reference ?: '—' }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($st->amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="signatures">
        <div>
            <p><strong>Le Bailleur (Lu et approuvé)</strong></p>
            <br><br><br>
            <p style="color: #94a3b8;">Signature</p>
        </div>
        <div style="text-align: right;">
            <p><strong>L'Agence Immobilière (Cachet & Signature)</strong></p>
            <br><br><br>
            <p style="color: #94a3b8;">Pour l'Agence</p>
        </div>
    </div>
</body>
</html>
