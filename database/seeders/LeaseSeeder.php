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
Entre les soussignés :

1. LE BAILLEUR :
{proprietaire_nom_complet}
Profession : {proprietaire_profession}
TEL : {proprietaire_telephone}

Représenté aux fins des présentes suivant mandat de gestion en date du {mandat_date} par « {agence_nom} » Agence immobilière, IFU N°{agence_nif_rccm}, représentée par son {agence_gerant_titre} {agence_gerant}, demeurant à {agence_adresse}, Tél : {agence_phone}, Pièce d’identité : {agence_gerant_piece}.
Ci-après dénommé « Le Bailleur »

ET

2. LE LOCATAIRE :
{locataire_nom_complet}
De nationalité {locataire_nationalite}, Pièce d’identité : {locataire_piece_identite}
Profession : {locataire_profession}
TEL : {locataire_telephone}
Ci-après dénommé « Le Locataire »

Il a été convenu ce qui suit :
La société {agence_nom}, représentant le bailleur {proprietaire_nom_complet}, donne en location à {locataire_nom_complet}, le locataire, {bien_titre} sise à {bien_adresse}, quartier {bien_quartier} de la ville de {bien_ville}.

Article 1er. : Durée du contrat
Le présent contrat est consenti et accepté pour une durée de {duree_mois} mois.
Le présent contrat entre en vigueur dès l'entrée du locataire dans les lieux loués, à savoir du {date_debut} au {date_fin}.
Dans l'hypothèse où l'entrée dans les lieux ne peut se produire à cette date, l'entrée en vigueur du présent contrat est retardée jusqu'à l'entrée effective du locataire dans les lieux loués.
La date d'entrée en vigueur est constatée dans le procès-verbal d'état des lieux, lequel est signé par les deux (02) parties, comme prévu à l'article 5 du présent contrat.
Il est renouvelable par tacite reconduction moyennant l’accord écrit des deux (02) parties contractantes au présent bail.
Le contrat peut prendre fin avant le terme dans les hypothèses visées aux articles 6 et 7 ci-dessous.

Article 2. : Loyer
Le présent contrat est consenti et accepté moyennant le montant de {loyer_montant}.
La somme ci-dessus mentionnée est payable mensuellement au plus tard le {jour_echeance} de chaque mois.
Le loyer est prépayé. Une caution d’un montant de {caution_montant} ({caution_mois} mois de loyer) est remise entre les mains du bailleur à la signature du contrat pour garantir les lieux loués. Les parties conviennent que la caution sera reversée au preneur en fin de bail, déduction faite de toutes les sommes qui pourraient être dues par celui-ci (travaux dus aux dégradations des lieux ayant résulté du fait du preneur, autres…).
Cependant si le montant à déduire est supérieur à la caution le preneur sera tenu de pourvoir au complément.

A l’entrée du locataire dans les lieux loués une somme totale de {total_entree_montant} est versée au bailleur représentant :
- Le paiement de {avance_mois} mois de loyer d’avance ;
- La caution de {caution_mois} mois de loyer ({caution_montant}).

Article 3. : Charges et obligations du locataire
3.1 : Occupation des lieux loués en bon père de famille
Le locataire s'engage à occuper et à entretenir l'ensemble des pièces, lieux et biens loués en bon père de famille pendant toute la durée du contrat. Le preneur supportera les réparations qui découleraient de son propre usage.
À la fin du contrat de bail, le locataire s'engage à restituer les lieux au bailleur dans un état conforme à celui dans lequel se trouvaient les lieux au moment de l'entrée en vigueur du présent contrat. Un état des lieux est établi avant l'entrée en vigueur du présent contrat de la façon décrite à l'article 5 ci-dessous. De même, un procès-verbal d'état de lieux sera établi lors de la fin de contrat de bail.

3.2 : Paiement du loyer
Le locataire s'engage à payer le loyer mensuellement au bailleur, dans les délais définis à l'article 2 du présent contrat.

3.3 : Charges du locataire
Les charges suivantes sont au compte du locataire :
- Eau ;
- Électricité ;
- Entretien du jardin.

3.4 : Frais d’enregistrement
Les frais d’enregistrement du présent contrat de bail sont à la charge du preneur.

Article 4. : Charges et responsabilités du bailleur
4.1 : Mise à disposition des lieux
Le bailleur s'engage à fournir des pièces, lieux et biens en bon état, permettant une occupation et une utilisation normale et viable de ces pièces, lieux et biens loués.

4.2 : Utilisation exclusive des lieux
Le bailleur a l’obligation d’assurer le droit d’utilisation exclusive des lieux au locataire.

4.3 : Grosses réparations
Le bailleur prendra à sa charge les grosses réparations telles que : les interventions nécessaires sur les murs, le toit, la plomberie, les cailloux sauvages, les dalles des fosses septiques, le sol du bâtiment.
Le preneur devra signaler au propriétaire toute dégradation affectant le bâtiment, les installations dans les meilleurs délais.

4.4 : Paiement de l’IRF
Apres enregistrement du contrat de bail par le locataire, le bailleur est tenu des paiements de l’impôt sur le revenu foncier dans les délais prescrits par la loi.

Article 5. : État des lieux
Il sera procédé un état des lieux en présence du locataire à son entrée, tous les trois (03) mois pendant son habitation et ainsi que lors de sa sortie du lieu loué. Il sera inventorié le mobilier incorporé à la construction, ainsi que l'état des infrastructures de la maison (robinets, WC, lavabos, etc.).
L’état des lieux sera signé par les deux (02) parties et annexé au présent contrat. Il sera également procédé au relevé des compteurs d'eau et d'électricité à ce moment.
Le jour de l'expiration du présent contrat de bail, le locataire devra remettre le local dans l'état décrit dans l'état des lieux. Sauf accord des parties l’état des lieux de sortie sera effectué le dernier jour de location et sera signé par les deux (02) parties.

Article 6. : Clause résolutoire
Le contrat est résolu de plein droit, lorsque l'une des parties ne remplit pas ses engagements ou ne respecte pas un article du présent contrat.
Une mise en demeure est adressée par la partie la plus diligente à la partie défaillante avec notification d'un préavis d'au moins deux (02) mois.

Article 7. : Résiliation unilatérale
Les parties doivent exécuter le contrat jusqu’à terme. Au cas où l’une des parties ne voudrait pas reconduire le contrat initial, il doit notifier un préavis écrit de deux (02) mois avant la fin du contrat à l’autre partie. Au cours de ce préavis, il sera autorisé au bailleur d’organiser d’éventuelles visites du local avec des clients potentiels en présence du locataire. En cas de résiliation de la part du locataire sans ce préavis, sa caution ne lui serait pas restituée. La résiliation du contrat avant le terme prévu initialement pour toute autre cause que sa fin normale engagera pour le locataire une pénalité de résiliation d’un (01) mois de loyer.

Article 8. : Résiliation de commun accord
En tout état de cause, il pourra être mis fin au présent contrat à tout moment, moyennant l'accord des parties.

Article 9. : Modifications du contrat
Toute modification apportée au présent contrat doit faire l'objet d’un avenant signé par les parties contractantes.

Article 10. : Différends
Tout litige résultant de la validité, de l’interprétation ou de l’exécution des présentes et de leur suite fera l’objet d’un règlement à l’amiable. A défaut de résolution amiable, les juridictions ordinaires sont compétentes.

Article 11. : Clause Diplomatique
Le présent bail peut être résilié par le locataire avec un préavis de trente (30) jours par simple lettre portée en main au bailleur, contre accusé de réception sans que le bailleur ait droit à aucune indemnité autre que le montant du loyer jusqu’à l’expiration du préavis dans les cas suivants :
- Reprise des locaux par le propriétaire pour usage personnel ;
- Défaut des consensus en cas d’augmentation de loyer ;

La résiliation sera faite sans préavis pour les motifs suivants :
- Pour défaut de payement de 01 mois de loyer au minimum et de 02 mois d’impayés au maximum.
A la fin du 2e mois impayé, le preneur prendra ses dispositions pour libérer immédiatement le local sans autre procédure judiciaire au plus tard le 28, 30 et 31 du deuxième mois impayé passé ce délai, l’Agence se réserve le droit de faire libérer le local le lendemain par ses moyens. Après cette libération du local par le preneur, un huissier de justice est alors saisi pour le recouvrement de la dette de l’agence au frais du preneur.
- Dégradation physique notoire des locaux par le preneur.

Cependant, le preneur qui souhaite résilier le contrat doit libérer les lieux au plus tard à la fin du mois (28, 29, 30 ou 31). Passé ce délai, le mois suivant est considéré comme un mois dû.

Toutefois, à la résiliation du présent bail, un état des lieux est fait à nouveau par les parties et le preneur est tenu de la remise des locaux à l’état initial en conformité avec l’état des lieux faites à l’entrée en jouissance. En fin de contrat, le preneur est tenu de résilier ses abonnements d’eau, d’électricité, de téléphone s’il y a lieu.

La résiliation pour toutes raisons autres que celles précédemment citées engageraient pour le preneur une pénalité de résiliation de 01 mois de loyer.

SIGNATURES POUR ACCORD EN QUATRE (04) EXEMPLAIRES ORIGINAUX
(Précédées de la mention « Lu et approuvé »)

Fait à {bien_ville}, le {date_du_jour}

POUR LE BAILLEUR                                    LE LOCATAIRE
{agence_nom}                                        {locataire_nom_complet}
{agence_gerant}
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
