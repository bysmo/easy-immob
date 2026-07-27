<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Relevé de compte Propriétaire - {{ $statement['owner']->full_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; color: #2563eb; text-transform: uppercase; }
        .box { border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin-bottom: 20px; background: #f9fafb; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; font-size: 13px; }
        .table th { background: #f3f4f6; text-transform: uppercase; font-size: 11px; }
        .summary-box { float: right; width: 300px; margin-top: 20px; border: 2px solid #2563eb; padding: 15px; border-radius: 8px; }
        .total { font-weight: bold; font-size: 16px; color: #2563eb; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimer ou Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <div class="title">Relevé de Compte Propriétaire</div>
        <div>Agence EasyImmob &mdash; Période : {{ $period ?: 'Toutes les périodes' }}</div>
    </div>

    <div class="box">
        <p><strong>Propriétaire :</strong> {{ $statement['owner']->full_name }} ({{ $statement['owner']->reference }})</p>
        <p><strong>Adresse :</strong> {{ $statement['owner']->address ?? '—' }}</p>
        <p><strong>Contact :</strong> {{ $statement['owner']->email }} / {{ $statement['owner']->phone }}</p>
        <p><strong>Nombre de biens gérés :</strong> {{ $statement['properties_count'] }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Période</th>
                <th>Bien</th>
                <th>Locataire</th>
                <th>Montant Perçu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statement['schedules'] as $sched)
                <tr>
                    <td>{{ $sched->period }}</td>
                    <td>{{ $sched->lease?->property?->title }}</td>
                    <td>{{ $sched->lease?->tenant?->full_name }}</td>
                    <td>{{ number_format((float)$sched->paid_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <p style="margin: 0; display: flex; justify-content: space-between;">
            <span>Loyers perçus :</span>
            <strong>{{ number_format($statement['total_collected'], 0, ',', ' ') }} FCFA</strong>
        </p>
        <p style="margin: 5px 0; display: flex; justify-content: space-between; color: #d97706;">
            <span>Frais d'agence ({{ $statement['management_fee_percentage'] }}%) :</span>
            <strong>- {{ number_format($statement['management_fee_amount'], 0, ',', ' ') }} FCFA</strong>
        </p>
        <hr style="border: 0; border-top: 1px solid #ccc; margin: 10px 0;">
        <p class="total" style="margin: 0; display: flex; justify-content: space-between;">
            <span>Net à Reverser :</span>
            <span>{{ number_format($statement['net_payable'], 0, ',', ' ') }} FCFA</span>
        </p>
    </div>

    <div style="clear: both; margin-top: 60px; text-align: right;">
        <p>Fait le {{ now()->format('d/m/Y') }} à Abidjan</p>
        <br><br>
        <p><strong>Le Responsable d'Agence (Cachet & Signature)</strong></p>
    </div>
</body>
</html>
