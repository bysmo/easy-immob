<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat de Bail {{ $lease->reference }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 20px; color: #1e293b; line-height: 1.6; font-size: 13px; }
        .no-print { margin-bottom: 20px; text-align: right; }
        .no-print button { padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .header { text-align: center; border-bottom: 2px solid #059669; padding-bottom: 15px; margin-bottom: 25px; }
        .agency-name { font-size: 22px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .agency-subtitle { font-size: 11px; color: #059669; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .agency-details { font-size: 11px; color: #64748b; }
        .contract-box-title { text-align: center; border: 2px solid #0f172a; padding: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 20px 0; background: #f8fafc; }
        .content { white-space: pre-wrap; font-size: 13px; text-align: justify; }
        .signatures { margin-top: 50px; display: table; width: 100%; page-break-inside: avoid; }
        .sig-box { display: table-cell; width: 50%; vertical-align: top; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Imprimer ou Enregistrer en PDF</button>
    </div>

    @php $agency = $lease->agency; @endphp
    <div class="header">
        @if($agency?->logo_url)
            <img src="{{ $agency->logo_url }}" alt="Logo {{ $agency->name }}" style="max-height: 70px; margin-bottom: 8px;">
        @endif
        <div class="agency-name">{{ $agency?->name ?? 'EasyImmob' }}</div>
        <div class="agency-subtitle">LOCATION & GESTION IMMOBILIÈRE</div>
        <div class="agency-details">
            @if($agency?->address) {{ $agency->address }} @endif
            @if($agency?->phone) | Tél: {{ $agency->phone }} @endif
            @if($agency?->email) | Email: {{ $agency->email }} @endif
            @if($agency?->nif_rccm) <br>N° Immat. / IFU : {{ $agency->nif_rccm }} @endif
        </div>
    </div>

    <div class="contract-box-title">
        CONTRAT DE BAIL À USAGE D’HABITATION
    </div>

    <div style="text-align: right; font-weight: bold; margin-bottom: 20px; font-size: 12px;">
        Référence du Bail : {{ $lease->reference }}
    </div>

    <div class="content">
        {{ $content }}
    </div>

    <div class="signatures">
        <div class="sig-box">
            <strong>POUR LE BAILLEUR / L'AGENCE</strong><br>
            <span style="font-size: 11px; font-style: italic;">Signature & Cachet (« Lu et approuvé »)</span>
            <br><br><br><br><br>
            <strong>{{ $agency?->name }}</strong>
        </div>
        <div class="sig-box">
            <strong>LE LOCATAIRE</strong><br>
            <span style="font-size: 11px; font-style: italic;">Signature précédée de « Lu et approuvé »</span>
            <br><br><br><br><br>
            <strong>{{ $lease->tenant?->full_name }}</strong>
        </div>
    </div>
</body>
</html>
