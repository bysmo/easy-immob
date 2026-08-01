<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\LeaseTemplate;
use Illuminate\Database\Seeder;

class LeaseSeeder extends Seeder
{
    public function run(): void
    {
        $agencies = Agency::all();

        $officialContent = <<<HTML
<div class="section-title">ENTRE LES SOUSSIGNÉS :</div>

<div class="party-card" style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
    <div style="font-weight: bold; color: #059669; text-transform: uppercase;">1. LE BAILLEUR (PROPRIÉTAIRE)</div>
    <div><strong>Nom & Prénoms :</strong> {proprietaire_nom_complet}</div>
    <div><strong>Profession :</strong> {proprietaire_profession} | <strong>Nationalité :</strong> {proprietaire_nationalite}</div>
    <div><strong>Téléphone :</strong> {proprietaire_telephone} | <strong>Email :</strong> {proprietaire_email}</div>
    <div><strong>Pièce d'identité :</strong> {proprietaire_piece_identite}</div>
    <div style="margin-top: 6px; font-size: 11px; color: #475569; border-top: 1px dashed #cbd5e1; padding-top: 4px;">
        <em>Représenté aux fins des présentes suivant mandat de gestion du <strong>{mandat_date}</strong> par l'Agence Immobilière <strong>{agence_nom}</strong> (IFU N°{agence_nif_rccm}), représentée par son {agence_gerant_titre} <strong>{agence_gerant}</strong>, demeurant à {agence_adresse}, Tél : {agence_phone}.</em>
    </div>
</div>

<div class="party-card" style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
    <div style="font-weight: bold; color: #059669; text-transform: uppercase;">2. LE LOCATAIRE</div>
    <div><strong>Nom & Prénoms :</strong> {locataire_nom_complet}</div>
    <div><strong>Profession :</strong> {locataire_profession} | <strong>Nationalité :</strong> {locataire_nationalite}</div>
    <div><strong>Téléphone :</strong> {locataire_telephone} | <strong>Email :</strong> {locataire_email}</div>
    <div><strong>Pièce d'identité :</strong> {locataire_piece_identite}</div>
</div>

<div class="section-title">BIEN IMMOBILIER CONCERNÉ</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">
    <thead>
        <tr style="background: #e2e8f0;">
            <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Bien / Titre</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Adresse & Situation</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Loyer Mensuel</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Caution (Dépôt)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px; font-weight: bold;">{bien_titre}</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">{bien_adresse}, quartier {bien_quartier}, {bien_ville}</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; color: #059669;">{loyer_montant}</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">{caution_montant} ({caution_mois} mois)</td>
        </tr>
    </tbody>
</table>

<div style="font-style: italic; margin-bottom: 15px;">Il a été convenu ce qui suit :</div>

<div class="article-title">Article 1er : Durée du contrat</div>
<p style="margin-top: 4px;">
Le présent contrat est consenti et accepté pour une durée de <strong>{duree_mois} mois</strong>.<br>
Le présent contrat entre en vigueur dès l'entrée du locataire dans les lieux loués, à savoir du <strong>{date_debut}</strong> au <strong>{date_fin}</strong>.<br>
Dans l'hypothèse où l'entrée dans les lieux ne peut se produire à cette date, l'entrée en vigueur du présent contrat est retardée jusqu'à l'entrée effective du locataire dans les lieux loués.<br>
La date d'entrée en vigueur est constatée dans le procès-verbal d'état des lieux, lequel est signé par les deux (02) parties.<br>
Il est renouvelable par tacite reconduction moyennant l’accord écrit des deux (02) parties contractantes au présent bail.
</p>

<div class="article-title">Article 2 : Loyer et caution</div>
<p style="margin-top: 4px;">
Le présent contrat est consenti et accepté moyennant le montant de <strong>{loyer_montant}</strong> payable mensuellement au plus tard le <strong>{jour_echeance}</strong> de chaque mois et prépayé.<br>
Une caution d’un montant de <strong>{caution_montant}</strong> ({caution_mois} mois de loyer) est remise entre les mains du bailleur/agence à la signature du contrat pour garantir les lieux loués. Les parties conviennent que la caution sera reversée au preneur en fin de bail, déduction faite de toutes les sommes qui pourraient être dues par celui-ci (travaux dus aux dégradations des lieux ayant résulté du fait du preneur).<br>
A l’entrée du locataire dans les lieux loués, une somme totale de <strong>{total_entree_montant}</strong> est versée au bailleur représentant :
</p>
<ul>
    <li>Le paiement de 2 mois de loyer d’avance ;</li>
    <li>La caution de {caution_mois} mois de loyer ({caution_montant}).</li>
</ul>

<div class="article-title">Article 3 : Charges et obligations du locataire</div>
<p style="margin-top: 4px;">
<strong>3.1 : Occupation des lieux loués en bon père de famille</strong><br>
Le locataire s'engage à occuper et à entretenir l'ensemble des pièces, lieux et biens loués en bon père de famille pendant toute la durée du contrat. Le preneur supportera les réparations qui découleraient de son propre usage. À la fin du contrat de bail, le locataire s'engage à restituer les lieux dans un état conforme à l'état des lieux d'entrée.<br>
<strong>3.2 : Paiement du loyer</strong><br>
Le locataire s'engage à payer le loyer mensuellement au bailleur/agence au plus tard le {jour_echeance} de chaque mois.<br>
<strong>3.3 : Charges du locataire</strong><br>
Les charges d'eau, d'électricité, de téléphone et d'entretien des jardins/espaces sont au compte du locataire.<br>
<strong>3.4 : Frais d’enregistrement</strong><br>
Les frais d’enregistrement du présent contrat de bail sont à la charge du preneur.
</p>

<div class="article-title">Article 4 : Charges et responsabilités du bailleur</div>
<p style="margin-top: 4px;">
<strong>4.1 : Mise à disposition des lieux</strong><br>
Le bailleur s'engage à fournir des pièces, lieux et biens en bon état, permettant une occupation normale et viable.<br>
<strong>4.2 : Utilisation exclusive des lieux</strong><br>
Le bailleur a l’obligation d’assurer le droit d’utilisation exclusive des lieux au locataire.<br>
<strong>4.3 : Grosses réparations</strong><br>
Le bailleur prendra à sa charge les grosses réparations telles que : les interventions nécessaires sur les murs, le toit, la plomberie principale, les dalles des fosses septiques, le sol du bâtiment.<br>
<strong>4.4 : Paiement de l’IRF</strong><br>
Après enregistrement du contrat de bail, le bailleur est tenu du paiement de l’impôt sur le revenu foncier dans les délais prescrits par la loi.
</p>

<div class="article-title">Article 5 : État des lieux</div>
<p style="margin-top: 4px;">
Il sera procédé à un état des lieux en présence du locataire à son entrée et lors de sa sortie du lieu loué. L'état des lieux sera signé par les deux (02) parties et annexé au présent contrat. Il sera également procédé au relevé des compteurs d'eau et d'électricité.
</p>

<div class="article-title">Article 6 : Clause résolutoire</div>
<p style="margin-top: 4px;">
Le contrat est résolu de plein droit lorsque l'une des parties ne remplit pas ses engagements ou ne respecte pas un article du présent contrat. Une mise en demeure est adressée par la partie la plus diligente à la partie défaillante avec notification d'un préavis d'au moins deux (02) mois.
</p>

<div class="article-title">Article 7 : Résiliation unilatérale</div>
<p style="margin-top: 4px;">
Les parties doivent exécuter le contrat jusqu’à terme. Au cas où l’une des parties ne voudrait pas reconduire le contrat initial, elle doit notifier un préavis écrit de deux (02) mois avant la fin du contrat à l’autre partie.
</p>

<div class="article-title">Article 8 : Résiliation de commun accord</div>
<p style="margin-top: 4px;">
En tout état de cause, il pourra être mis fin au présent contrat à tout moment, moyennant l'accord écrit des parties.
</p>

<div class="article-title">Article 9 : Modifications du contrat</div>
<p style="margin-top: 4px;">
Toute modification apportée au présent contrat doit faire l'objet d’un avenant signé par les parties contractantes.
</p>

<div class="article-title">Article 10 : Différends</div>
<p style="margin-top: 4px;">
Tout litige résultant de la validité, de l’interprétation ou de l’exécution des présentes fera l’objet d’un règlement à l’amiable. À défaut de resolution amiable, les juridictions ordinaires sont compétentes.
</p>

<div class="article-title">Article 11 : Clause Diplomatique et libération des lieux</div>
<p style="margin-top: 4px;">
Le présent bail peut être résilié par le locataire avec un préavis de trente (30) jours par simple lettre portée en main au bailleur contre accusé de réception.<br>
En cas de défaut de paiement de 01 à 02 mois de loyer, la résiliation est faite sans préavis et le locataire libérera le local sans autre procédure judiciaire.
</p>

<div style="margin-top: 25px; font-weight: bold; text-align: center;">
SIGNATURES POUR ACCORD EN QUATRE (04) EXEMPLAIRES ORIGINAUX<br>
<span style="font-size: 11px; font-weight: normal; font-style: italic;">(Précédées de la mention « Lu et approuvé »)</span>
</div>

<div style="margin-top: 20px;">
Fait à {bien_ville}, le {date_du_jour}
</div>
HTML;

        foreach ($agencies as $agency) {
            LeaseTemplate::withoutGlobalScopes()->updateOrCreate([
                'agency_id' => $agency->id,
                'name'      => 'Contrat d\'habitation type (Bail à usage d\'habitation)',
            ], [
                'description' => 'Modèle officiel d\'un bail à usage d\'habitation avec l\'ensemble des 11 articles régissant la location.',
                'content'     => $officialContent,
                'version'     => 2,
                'status'      => 'active',
            ]);
        }
    }
}
