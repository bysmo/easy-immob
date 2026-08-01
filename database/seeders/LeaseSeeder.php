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

        $officialContent = <<<TEXT
CONTRAT DE BAIL À USAGE D’HABITATION

Entre les soussignés :

1. LE BAILLEUR :
{proprietaire_nom_complet}
Profession : {proprietaire_profession}
TEL : {proprietaire_telephone}
Représenté aux fins des présentes suivant mandat de gestion en date du {mandat_date} par « {agence_nom} » Agence immobilière, IFU N°{agence_nif_rccm}, représentée par son Gérant {agence_gerant}, demeurant à {agence_adresse}, Tél : {agence_phone}.
Ci-après dénommé « Le Bailleur »

ET

2. LE LOCATAIRE :
{locataire_nom_complet}
De nationalité {locataire_nationalite}, Pièce d’identité : {locataire_piece_identite}
Profession : {locataire_profession}
TEL : {locataire_telephone}
Ci-après dénommé « Le Locataire »

Il a été convenu ce qui suit :
La société {agence_nom}, représentant le bailleur {proprietaire_nom_complet}, donne en location à {locataire_nom_complet}, le locataire, {bien_titre} sise à {bien_adresse}, quartier {bien_quartier}, ville de {bien_ville}.

Article 1er : Durée du contrat
Le présent contrat est consenti et accepté pour une durée de {duree_mois} mois.
Le présent contrat entre en vigueur dès l'entrée du locataire dans les lieux loués, à savoir du {date_debut} au {date_fin}.
Dans l'hypothèse où l'entrée dans les lieux ne peut se produire à cette date, l'entrée en vigueur du présent contrat est retardée jusqu'à l'entrée effective du locataire dans les lieux loués.
La date d'entrée en vigueur est constatée dans le procès-verbal d'état des lieux, lequel est signé par les deux (02) parties.
Il est renouvelable par tacite reconduction moyennant l’accord écrit des deux (02) parties contractantes au présent bail.

Article 2 : Loyer et caution
Le présent contrat est consenti et accepté moyennant le montant de {loyer_montant} payable mensuellement et prépayé.
Une caution d’un montant de {caution_montant} ({caution_mois} mois de loyer) est remise entre les mains du bailleur/agence à la signature du contrat pour garantir les lieux loués. Les parties conviennent que la caution sera reversée au preneur en fin de bail, déduction faite de toutes les sommes qui pourraient être dues par celui-ci (travaux dus aux dégradations des lieux ayant résulté du fait du preneur).
A l’entrée du locataire dans les lieux loués, une somme totale de {total_entree_montant} est versée au bailleur représentant :
- Le paiement de 2 mois de loyer d’avance ;
- La caution de {caution_mois} mois de loyer ({caution_montant}).

Article 3 : Charges et obligations du locataire
3.1 : Occupation des lieux loués en bon père de famille
Le locataire s'engage à occuper et à entretenir l'ensemble des pièces, lieux et biens loués en bon père de famille pendant toute la durée du contrat. Le preneur supportera les réparations qui découleraient de son propre usage. À la fin du contrat de bail, le locataire s'engage à restituer les lieux dans un état conforme à l'état des lieux d'entrée.
3.2 : Paiement du loyer
Le locataire s'engage à payer le loyer mensuellement au bailleur/agence au plus tard le {jour_echeance} de chaque mois.
3.3 : Charges du locataire
Les charges d'eau, d'électricité, de téléphone et d'entretien des jardins/espaces sont au compte du locataire.
3.4 : Frais d’enregistrement
Les frais d’enregistrement du présent contrat de bail sont à la charge du preneur.

Article 4 : Charges et responsabilités du bailleur
4.1 : Mise à disposition des lieux
Le bailleur s'engage à fournir des pièces, lieux et biens en bon état, permettant une occupation normale et viable.
4.2 : Utilisation exclusive des lieux
Le bailleur a l’obligation d’assurer le droit d’utilisation exclusive des lieux au locataire.
4.3 : Grosses réparations
Le bailleur prendra à sa charge les grosses réparations telles que : les interventions nécessaires sur les murs, le toit, la plomberie principale, les dalles des fosses septiques, le sol du bâtiment.
4.4 : Paiement de l’IRF
Après enregistrement du contrat de bail, le bailleur est tenu du paiement de l’impôt sur le revenu foncier dans les délais prescrits par la loi.

Article 5 : État des lieux
Il sera procédé à un état des lieux en présence du locataire à son entrée et lors de sa sortie du lieu loué. L'état des lieux sera signé par les deux (02) parties et annexé au présent contrat. Il sera également procédé au relevé des compteurs d'eau et d'électricité.

Article 6 : Clause résolutoire
Le contrat est résolu de plein droit lorsque l'une des parties ne remplit pas ses engagements ou ne respecte pas un article du présent contrat. Une mise en demeure est adressée par la partie la plus diligente à la partie défaillante avec notification d'un préavis d'au moins deux (02) mois.

Article 7 : Résiliation unilatérale
Les parties doivent exécuter le contrat jusqu’à terme. Au cas où l’une des parties ne voudrait pas reconduire le contrat initial, elle doit notifier un préavis écrit de deux (02) mois avant la fin du contrat à l’autre partie.

Article 8 : Résiliation de commun accord
En tout état de cause, il pourra être mis fin au présent contrat à tout moment, moyennant l'accord écrit des parties.

Article 9 : Modifications du contrat
Toute modification apportée au présent contrat doit faire l'objet d’un avenant signé par les parties contractantes.

Article 10 : Différends
Tout litige résultant de la validité, de l’interprétation ou de l’exécution des présentes fera l’objet d’un règlement à l’amiable. À défaut de résolution amiable, les juridictions ordinaires sont compétentes.

Article 11 : Clause Diplomatique et libération des lieux
Le présent bail peut être résilié par le locataire avec un préavis de trente (30) jours par simple lettre portée en main au bailleur contre accusé de réception.
En cas de défaut de paiement de 01 à 02 mois de loyer, la résiliation est faite sans préavis et le locataire libérera le local sans autre procédure judiciaire.

SIGNATURES POUR ACCORD EN QUATRE (04) EXEMPLAIRES ORIGINAUX
(Précédées de la mention « Lu et approuvé »)

Fait à {bien_ville}, le {date_du_jour}

POUR LE BAILLEUR / L'AGENCE                       LE LOCATAIRE
{agence_nom}                                     {locataire_nom_complet}
TEXT;

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
