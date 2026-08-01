<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mandat de Gestion {{ $contract->reference }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 13px; line-height: 1.6; margin: 0; padding: 20px; }
        .no-print { margin-bottom: 20px; text-align: right; }
        .no-print button { padding: 10px 20px; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .header { text-align: center; border-bottom: 2px solid #0284c7; padding-bottom: 15px; margin-bottom: 25px; }
        .agency-name { font-size: 22px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .agency-subtitle { font-size: 11px; color: #0284c7; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .agency-details { font-size: 11px; color: #64748b; }
        .contract-box-title { text-align: center; border: 2px solid #0f172a; padding: 10px; font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 20px 0; background: #f8fafc; }
        .section-title { font-size: 14px; font-weight: bold; color: #0f172a; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-top: 20px; margin-bottom: 10px; text-transform: uppercase; }
        .grid-2 { display: table; width: 100%; margin-bottom: 15px; }
        .col-6 { display: table-cell; width: 50%; vertical-align: top; }
        .article-title { font-weight: bold; color: #0f172a; margin-top: 15px; text-decoration: underline; }
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

    @php $agency = $contract->agency; $owner = $contract->owner; @endphp

    <div class="header">
        @if($agency?->logo_url)
            <img src="{{ $agency->logo_url }}" alt="Logo {{ $agency->name }}" style="max-height: 70px; margin-bottom: 8px;">
        @endif
        <div class="agency-name">{{ $agency?->name ?? 'KIPRESS ESTATE' }}</div>
        <div class="agency-subtitle">ACHAT - VENTE - LOCATION - GESTION IMMOBILIÈRE</div>
        <div class="agency-details">
            @if($agency?->address) {{ $agency->address }} @endif
            @if($agency?->phone) | Tél : {{ $agency->phone }} @endif
            @if($agency?->email) | Email : {{ $agency->email }} @endif
            @if($agency?->nif_rccm) <br>IFU / RCCM : {{ $agency->nif_rccm }} @endif
        </div>
    </div>

    <div class="contract-box-title">
        MANDAT DE GESTION IMMOBILIÈRE
    </div>

    <div style="display: table; width: 100%; margin-bottom: 20px; font-weight: bold;">
        <div style="display: table-cell; width: 50%;">DATE : {{ $contract->start_date?->format('d/m/Y') }}</div>
        <div style="display: table-cell; width: 50%; text-align: right;">N° DOSSIER : {{ $contract->reference }}</div>
    </div>

    <div class="section-title">ENTRE LES SOUSSIGNÉS :</div>

    <div style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
        <div style="font-weight: bold; color: #0284c7; text-transform: uppercase;">LE MANDANT (PROPRIÉTAIRE)</div>
        <div><strong>Nom & Prénoms :</strong> {{ $owner?->full_name }}</div>
        <div><strong>Adresse actuelle :</strong> {{ $owner?->address ?? 'N/A' }}</div>
        <div><strong>Téléphone :</strong> {{ $owner?->phone }}</div>
        <div><strong>Email :</strong> {{ $owner?->email ?? 'N/A' }}</div>
        <div><strong>Pièce d'identité :</strong> {{ $owner?->identity_document ?? 'CNIB/Passeport N/A' }}</div>
    </div>

    <div style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
        <div style="font-weight: bold; color: #0284c7; text-transform: uppercase;">LE MANDATAIRE (AGENCE IMMOBILIÈRE)</div>
        <div><strong>Société :</strong> {{ $agency?->legal_name ?? $agency?->name }}</div>
        <div><strong>Représentée par :</strong> Son Gérant / Responsable Légal</div>
        <div><strong>Adresse :</strong> {{ $agency?->address }}</div>
        <div><strong>Téléphone / Email :</strong> {{ $agency?->phone }} / {{ $agency?->email }}</div>
    </div>

    <div class="section-title">BIEN(S) IMMOBILIER(S) CONCERNÉ(S)</div>
    @if($contract->properties->count() > 0)
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">
            <thead>
                <tr style="background: #e2e8f0;">
                    <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Bien / Titre</th>
                    <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Adresse & Situation</th>
                    <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Loyer mensuel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->properties as $prop)
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $prop->title }} ({{ $prop->reference }})</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $prop->address }}, {{ $prop->city }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">{{ number_format((float)$prop->rent_amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Loyer prévisionnel estimé : <strong>{{ number_format((float)($contract->agreed_rent_amount ?? 0), 0, ',', ' ') }} FCFA</strong></p>
    @endif

    <div style="font-style: italic; margin-bottom: 15px;">Il a été convenu et arrêté ce qui suit :</div>

    <div class="article-title">Article 1 : Objet du contrat</div>
    <p style="margin-top: 4px;">
        Par le présent contrat, le Mandant confie exclusivement à <strong>{{ $agency?->name }}</strong> la gestion et l’administration des locaux susdésignés.<br>
        Le loyer mensuel proposé ou accepté par le mandant fera l'objet de confirmation par contrat de bail. Dans tous les cas, le prix figurant sur le contrat de bail fera foi entre les parties au présent contrat. {{ $agency?->name }} déclare expressément accepter le mandat qui vient de lui être donné aux clauses et aux conditions ci-après.
    </p>

    <div class="article-title">Article 2 : Pouvoirs du mandataire</div>
    <p style="margin-top: 4px;">
        Par le présent contrat, le mandataire est chargé de négocier et de conclure des contrats de bail au nom et pour le compte du mandant. Le mandataire négociera, conclura, exécutera et modifiera tout contrat de bail commercial, d’habitation ou à usage mixte, écrit aux charges et conditions prévues par les dispositions légales en vigueur.<br>
        Il dressera avant chaque entrée et sortie du locataire un procès-verbal d’état des lieux. Il informera le mandant dans les huit (08) jours qui suivront de la signature d’un contrat de bail avec un locataire et fournira au mandant une copie dudit contrat. Il poursuivra le recouvrement amiable et au besoin contentieux des loyers.
        L’Impôt sur le Revenu Foncier (IRF) sera supporté par le mandant qui fournira les quittances à l'administration fiscale.
    </p>

    <div class="article-title">Article 3 : Condition de la gestion</div>
    <p style="margin-top: 4px;"><strong>A. Obligations du Mandataire :</strong><br>
    Le mandataire s’engage à gérer et administrer le bien confié en bon professionnel. Il produira mensuellement un récapitulatif de compte et reversera le loyer sous huitaine à compter du paiement effectif du locataire. Il observe la plus stricte confidentialité des informations transmises par le mandant.</p>

    <p style="margin-top: 4px;"><strong>B. Obligations du Mandant :</strong><br>
    Le Mandant s’engage à confier la gestion et l’administration du bien exclusivement à {{ $agency?->name }} durant toute la période du présent contrat. Il s’engage, pendant la durée du mandat et pendant une période de trois (03) mois suivant son expiration, à ne pas louer directement à un candidat présenté par le Mandataire (peine de pénalité d'un mois de loyer).</p>

    <div class="article-title">Article 4 : Reddition des comptes</div>
    <p style="margin-top: 4px;">
        {{ $agency?->name }} est tenue de rendre compte de sa gestion au Mandant à chaque fin de mois et verser à l’appui les sommes d’argent qui lui reviennent. Un relevé détaillé est dressé mensuellement à l’intention du Mandant.
    </p>

    <div class="article-title">Article 5 : Rémunération</div>
    <p style="margin-top: 4px;">
        Pour sa gestion, le Mandataire percevra chaque mois une commission de : <strong>{{ $contract->formatted_commission }}</strong>.<br>
        Le Mandant consent à ce que le montant de cette commission soit directement prélevé sur les loyers perçus avant reversement. En sa qualité de mandataire, {{ $agency?->name }} conservera la caution versée par le locataire et la reversera en fin de bail contre état des lieux conforme.
    </p>

    <div class="article-title">Article 6 : Durée – Modification – Rupture</div>
    <p style="margin-top: 4px;">
        Le présent contrat est conclu pour une durée de <strong>{{ $contract->duration_months }} mois</strong>. Il pourra être résilié moyennant un préavis écrit de <strong>{{ $contract->notice_period_months }} mois</strong> notification faite par lettre recommandée avec accusé de réception.
    </p>

    <div class="article-title">Article 7 : Droit applicable</div>
    <p style="margin-top: 4px;">
        Le présent contrat est régi par l’Acte Uniforme OHADA relatif au droit commercial général et par les lois en vigueur.
    </p>

    <div class="article-title">Article 8 : Différends</div>
    <p style="margin-top: 4px;">
        Tout litige résultant de la validité, de l’interprétation ou de l’exécution des présentes fera l’objet d’un règlement à l’amiable, et à défaut par les juridictions compétentes.
    </p>

    <div class="article-title">Article 9 : Élection de domicile</div>
    <p style="margin-top: 4px;">
        Les parties font élection de domicile en leurs sièges/domiciles respectifs.<br>
        <strong>Mode de règlement / Compte N° :</strong> {{ $contract->payment_bank_details ?? 'Compte désigné par le mandant' }}
    </p>

    <div style="margin-top: 30px;">
        Fait à {{ $agency?->city ?? 'Ouagadougou' }}, le {{ $contract->start_date?->format('d/m/Y') }}<br>
        En deux (02) exemplaires originaux.
    </div>

    <div class="signatures">
        <div class="sig-box">
            <strong>LE MANDANT (PROPRIÉTAIRE)</strong><br>
            <span style="font-size: 11px; font-style: italic;">Signature précédée de « Lu et approuvé »</span>
            <br><br><br><br><br>
            <strong>{{ $owner?->full_name }}</strong>
        </div>
        <div class="sig-box">
            <strong>LE MANDATAIRE (L'AGENCE)</strong><br>
            <span style="font-size: 11px; font-style: italic;">Signature précédée de « Lu et approuvé »</span>
            <br><br><br><br><br>
            <strong>{{ $agency?->name }}</strong>
        </div>
    </div>
</body>
</html>
