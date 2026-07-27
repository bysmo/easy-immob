<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Quittance de loyer - {{ $schedule->period }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #059669; padding-bottom: 15px; margin-bottom: 30px; }
        .title { font-size: 22px; font-weight: bold; color: #059669; text-transform: uppercase; }
        .subtitle { font-size: 14px; color: #666; }
        .box { border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin-bottom: 20px; background: #f9fafb; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        .table th { background: #f3f4f6; font-size: 12px; text-transform: uppercase; }
        .total { font-weight: bold; font-size: 16px; color: #059669; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimer la quittance / Export PDF
        </button>
    </div>

    <div class="header">
        <div class="title">Quittance de Loyer</div>
        <div class="subtitle">Période du loyer : {{ $schedule->period }}</div>
    </div>

    <div class="box">
        <p><strong>Agence :</strong> {{ $schedule->lease?->agency?->name }}</p>
        <p><strong>Bailleur (Propriétaire) :</strong> {{ $schedule->lease?->property?->owner?->full_name }}</p>
        <p><strong>Locataire :</strong> {{ $schedule->lease?->tenant?->full_name }}</p>
        <p><strong>Adresse du bien loué :</strong> {{ $schedule->lease?->property?->title }} - {{ $schedule->lease?->property?->address }}, {{ $schedule->lease?->property?->city }}</p>
    </div>

    <p>Je soussigné, représentant de l'agence <strong>{{ $schedule->lease?->agency?->name }}</strong>, reconnais avoir reçu de M./Mme <strong>{{ $schedule->lease?->tenant?->full_name }}</strong> la somme indiquée ci-dessous, en règlement du loyer et des charges pour la période de <strong>{{ $schedule->period }}</strong>.</p>

    <table class="table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyer principal (Hors charges)</td>
                <td>{{ number_format((float)$schedule->lease?->rent_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Provision pour charges</td>
                <td>{{ number_format((float)$schedule->lease?->charges_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total">
                <td>Total reçu (Quittance)</td>
                <td>{{ number_format((float)$schedule->expected_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 12px; color: #666; margin-top: 15px;">
        Cette quittance annule tous les reçus provisoires qui auraient pu être délivrés pour cette même période.
    </p>

    <div style="margin-top: 50px; text-align: right;">
        <p>Fait le {{ now()->format('d/m/Y') }} à Abidjan</p>
        <br><br>
        <p><strong>Pour l'Agence (Cachet & Signature)</strong></p>
    </div>
</body>
</html>
