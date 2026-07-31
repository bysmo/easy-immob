<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat {{ $lease->reference }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .content { white-space: pre-wrap; font-size: 14px; }
        .footer { margin-top: 50px; display: flex; justify-between: space-between; }
        .signature-box { width: 45%; border-top: 1px solid #ccc; pt: 10px; margin-top: 60px; font-size: 12px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimer ou Enregistrer en PDF
        </button>
    </div>

    @php $agency = $lease->agency; @endphp
    <div class="header" style="text-align: center;">
        @if($agency?->logo_url)
            <img src="{{ $agency->logo_url }}" alt="Logo {{ $agency->name }}" style="max-height: 75px; max-width: 250px; margin-bottom: 10px;">
        @endif
        <div class="title" style="font-size: 22px; font-weight: bold; text-transform: uppercase;">{{ $agency?->name ?? 'EasyImmob' }}</div>
        @if($agency?->legal_name)
            <div style="font-size: 12px; color: #64748b; font-style: italic;">{{ $agency->legal_name }}</div>
        @endif
        <div style="font-size: 12px; color: #475569; margin-top: 3px;">
            @if($agency?->address) {{ $agency->address }} @endif
            @if($agency?->phone) | Tél: {{ $agency->phone }} @endif
            @if($agency?->email) | Email: {{ $agency->email }} @endif
        </div>
        @if($agency?->nif_rccm)
            <div style="font-size: 11px; color: #64748b;">N° Immat. / NIF : {{ $agency->nif_rccm }}</div>
        @endif
        <div style="font-size: 14px; font-weight: bold; margin-top: 10px; color: #059669; text-transform: uppercase;">Contrat de location N° {{ $lease->reference }}</div>
    </div>

    <div class="content">
        {{ $content }}
    </div>

    <div style="margin-top: 60px; display: table; width: 100%;">
        <div style="display: table-cell; width: 50%;">
            <strong>Le Bailleur / L'Agence</strong><br><br><br><br>
            Signature & Cachet
        </div>
        <div style="display: table-cell; width: 50%;">
            <strong>Le Locataire</strong><br><br><br><br>
            Signature ("Lu et approuvé")
        </div>
    </div>
</body>
</html>
