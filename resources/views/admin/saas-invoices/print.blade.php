<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture SaaS {{ $invoice->number }} — EasyImmob</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #fff;
            color: #1e293b;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #e2e8f0;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
            color: #059669;
        }
        .subbrand {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            margin: 0;
            font-size: 22px;
            color: #0f172a;
        }
        .invoice-number {
            font-family: monospace;
            font-weight: bold;
            color: #059669;
            font-size: 15px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .box {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }
        .box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f1f5f9;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }
        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total-box {
            float: right;
            width: 300px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 20px;
            border-radius: 8px;
            text-align: right;
        }
        .total-amount {
            font-size: 22px;
            font-weight: 800;
            color: #047857;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-unpaid { background-color: #fef3c7; color: #92400e; }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px auto; text-align: right;">
        <button onclick="window.print()" style="background-color: #059669; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            🖨️ Imprimer la Facture
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="brand">EasyImmob</div>
                <div class="subbrand">Plateforme de Gestion Locative SaaS</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Support Client : support@easyimmob.com<br>
                    Abidjan, Côte d'Ivoire
                </div>
            </div>
            <div class="invoice-title">
                <h1>FACTURE SAAS</h1>
                <div class="invoice-number">{{ $invoice->number }}</div>
                <div style="margin-top: 6px;">
                    <span class="badge {{ $invoice->status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                        {{ $invoice->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <div class="details-grid">
            <div class="box">
                <div class="box-title">Facturé à (Agence Immobilière)</div>
                <div style="font-weight: 700; font-size: 16px; color: #0f172a;">{{ $invoice->agency?->name }}</div>
                <div>{{ $invoice->agency?->legal_name ?? $invoice->agency?->name }}</div>
                <div>Email : {{ $invoice->agency?->email }}</div>
                <div>Téléphone : {{ $invoice->agency?->phone ?? 'N/A' }}</div>
                <div>Adresse : {{ $invoice->agency?->address ?? 'N/A' }}</div>
            </div>

            <div class="box">
                <div class="box-title">Informations Facture</div>
                <div><strong>Date d'émission :</strong> {{ $invoice->invoice_date?->format('d/m/Y') }}</div>
                <div><strong>Date d'échéance :</strong> {{ $invoice->due_date?->format('d/m/Y') }}</div>
                <div><strong>Cycle de Facturation :</strong> {{ $invoice->billing_cycle === 'yearly' ? 'Annuel' : 'Mensuel' }}</div>
                @if($invoice->paid_at)
                    <div><strong>Date de paiement :</strong> {{ $invoice->paid_at?->format('d/m/Y H:i') }}</div>
                    <div><strong>Mode de règlement :</strong> {{ $invoice->payment_method }}</div>
                @endif
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Désignation de la prestation SaaS</th>
                    <th>Cycle</th>
                    <th style="text-align: right;">Prix unitaire</th>
                    <th style="text-align: right;">Montant Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="color: #0f172a;">Abonnement SaaS EasyImmob — {{ $invoice->subscriptionPlan?->name ?? 'Plan personnalisé' }}</strong>
                        <div style="font-size: 12px; color: #64748b;">
                            Accès à la plateforme et gestion jusqu'à {{ $invoice->subscriptionPlan?->isUnlimitedProperties() ? 'Biens Illimités' : $invoice->subscriptionPlan?->max_properties . ' biens à louer' }}
                        </div>
                    </td>
                    <td style="text-transform: capitalize;">{{ $invoice->billing_cycle === 'yearly' ? '1 An' : '1 Mois' }}</td>
                    <td style="text-align: right;">{{ $invoice->formatted_total }}</td>
                    <td style="text-align: right; font-weight: 700; color: #0f172a;">{{ $invoice->formatted_total }}</td>
                </tr>
            </tbody>
        </table>

        <div style="overflow: hidden; margin-bottom: 20px;">
            <div class="total-box">
                <div style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: #047857;">Total à Payer / Acquitté</div>
                <div class="total-amount">{{ $invoice->formatted_total }}</div>
                <div style="font-size: 11px; color: #059669; margin-top: 4px;">TVA Exonérée (SaaS B2B)</div>
            </div>
        </div>

        @if($invoice->notes)
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; font-size: 12px; color: #475569; clear: both; margin-top: 20px;">
                <strong>Notes :</strong> {{ $invoice->notes }}
            </div>
        @endif

        <div class="footer">
            Facture générée automatiquement par la plateforme SaaS EasyImmob — Document officiel de facturation.<br>
            Merci de votre confiance et de votre partenariat.
        </div>
    </div>
</body>
</html>
