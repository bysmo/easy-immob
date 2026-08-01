<?php

namespace App\Domain\Owner\Services;

use App\Domain\Owner\Models\ManagementContract;

class ManagementContractGenerator
{
    public function generateText(ManagementContract $contract): string
    {
        $contract->loadMissing(['agency', 'owner', 'properties']);
        $agency = $contract->agency;
        $owner = $contract->owner;
        $properties = $contract->properties;

        $startDateFormatted = $contract->start_date ? $contract->start_date->format('d/m/Y') : now()->format('d/m/Y');
        $endDateFormatted = $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Conduite alignée sur les baux ou renouvelable';
        $commissionStr = $contract->formatted_commission;

        $propertiesList = '';
        if ($properties->count() > 0) {
            foreach ($properties as $prop) {
                $propertiesList .= "- {$prop->title} sis à {$prop->address}, {$prop->city} (Quartier: {$prop->neighborhood}, Loyer: " . number_format((float)$prop->rent_amount, 0, ',', ' ') . " FCFA)\n";
            }
        } else {
            $propertiesList = "- Bien(s) désigné(s) sous la référence " . ($contract->agreed_rent_amount ? 'Loyer prévisionnel : ' . number_format((float)$contract->agreed_rent_amount, 0, ',', ' ') . ' FCFA' : 'N/A') . "\n";
        }

        return <<<TEXT
MANDAT DE GESTION IMMOBILIÈRE

Référence / N° Dossier : {$contract->reference}
Date d'effet : {$startDateFormatted}

ENTRE LES SOUSSIGNÉS :

LE MANDANT (PROPRIÉTAIRE) :
Nom & Prénoms / Raison sociale : {$owner->full_name}
Adresse : {$owner->address}
Téléphone : {$owner->phone}
Email : {$owner->email}
Pièce d'identité : {$owner->identity_document}

Ci-après dénommé(e) « Le Mandant »

ET

LE MANDATAIRE (AGENCE IMMOBILIÈRE) :
La société {$agency->name}
Représentée par son Gérant / Responsable légal
Téléphone : {$agency->phone}
Email : {$agency->email}
Adresse : {$agency->address}
N° IFU / RCCM : {$agency->nif_rccm}

Ci-après dénommé « Le Mandataire »

Il a été convenu et arrêté ce qui suit :

Article 1 : Objet du contrat
Par le présent contrat, le Mandant confie exclusivement au Mandataire la gestion et l’administration du ou des bien(s) immobilier(s) suivant(s) :
{$propertiesList}
Le prix du loyer figurant sur chaque contrat de bail fera foi entre les parties au présent contrat.
Le Mandataire déclare expressément accepter le mandat qui vient de lui être donné aux clauses et conditions ci-après.

Article 2 : Pouvoirs du mandataire
Par le présent contrat, le Mandataire est chargé de négocier et de conclure des contrats de bail au nom et pour le compte du Mandant. Le Mandataire négociera, conclura, exécutera et modifiera tout contrat de bail (commercial, d’habitation ou à usage mixte) aux charges et conditions prévues par les dispositions légales en vigueur et dans le respect des intérêts du Mandant.
Il fera toute publicité qu’il jugera nécessaire en vue de la location de l’immeuble. Il dressera un procès-verbal d’état des lieux à chaque entrée et sortie de locataire.
Il informera le Mandant dans les huit (08) jours qui suivront la signature d’un contrat de bail et lui en fournira copie. Il poursuivra le recouvrement des loyers échus ou à échoir.

Article 3 : Conditions de la gestion

A. Obligations du Mandataire :
Le Mandataire s’engage à gérer et administrer le bien en bon professionnel et suivant les règles de l’art.
- Produire un récapitulatif de compte mensuel et reverser le loyer net sous huitaine à compter du paiement effectif par le locataire.
- Mettre tout en œuvre pour un recouvrement rapide des loyers et sélectionner rigoureusement les locataires.
- User du droit de visite pour s’assurer du bon entretien des lieux loués.
- Observer la plus stricte confidentialité quant aux informations transmises par le Mandant.

B. Obligations du Mandant :
- Confier la gestion et l’administration du bien exclusivement au Mandataire pendant toute la durée du contrat, et s’abstenir de gérer personnellement ou de passer d’autres mandats.
- Ne pas louer directement à un candidat présenté par le Mandataire pendant la durée du mandat et durant trois (03) mois suivant son expiration (sous peine d’une pénalité égale à un mois de loyer).
- Fournir les justifications de propriété et laisser le Mandataire visiter les lieux.
- Entretenir les locaux de façon à ce qu’ils soient attrayants pour les locataires potentiels et assurer le gardiennage/enlèvement des ordures durant les périodes d’inoccupation.

Article 4 : Reddition des comptes
Le Mandataire est tenu de rendre compte de sa gestion au Mandant à chaque fin de mois et de verser à l’appui les sommes d’argent lui revenant. Un relevé détaillé est dressé mensuellement, outre une situation annuelle sur demande du Mandant.

Article 5 : Rémunération
En contrepartie de sa gestion, le Mandataire percevra chaque mois une commission de : {$commissionStr}.
Le Mandant consent à ce que le montant de cette commission soit directement prélevé sur les loyers perçus avant reversement.
En sa qualité de mandataire, l’Agence conservera entre ses mains la caution versée par le locataire pour la garantie de ses obligations et la reversera en fin de bail contre remise en état des lieux.

Article 6 : Durée – Modification – Rupture
Le présent contrat est conclu pour une durée de {$contract->duration_months} mois à compter du {$startDateFormatted}.
Il peut être résilié de plein droit en cas de non-respect par l’une des parties de ses obligations 8 jours après mise en demeure restée infructueuse. Toute dénonciation ou non-renouvellement doit être notifié par lettre avec accusé de réception moyennant un préavis de {$contract->notice_period_months} mois.

Article 7 : Droit applicable
Le présent contrat est régi par l’Acte Uniforme OHADA relatif au droit commercial général et la réglementation immobilière en vigueur.

Article 8 : Différends
Tout litige relatif à la validité, l’interprétation ou l’exécution du présent contrat fera l’objet d’un règlement à l’amiable. À défaut, les juridictions compétentes seront saisies.

Article 9 : Élection de domicile & Règlement
Pour l’exécution des présentes, les parties font élection de domicile :
- Pour le Mandataire, en son siège social.
- Pour le Mandant, en son domicile susmentionné.
Mode de règlement / Coordonnées bancaires du Mandant : {$contract->payment_bank_details}

Fait à {$agency->city}, le {$startDateFormatted}
En deux (02) exemplaires originaux.

LE MANDANT (PROPRIÉTAIRE)                    LE MANDATAIRE (L'AGENCE)
Signature précédée de                        Signature précédée de
la mention « Lu et approuvé »                la mention « Lu et approuvé »
TEXT;
    }
}
