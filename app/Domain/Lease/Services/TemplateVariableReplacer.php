<?php

namespace App\Domain\Lease\Services;

use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;

class TemplateVariableReplacer
{
    /**
     * Remplace les variables dynamiques dans un modèle de contrat de bail.
     */
    public function replaceForLease(string $templateContent, Lease $lease): string
    {
        $lease->loadMissing(['property.owner', 'property.managementContract', 'tenant', 'agency']);

        $property = $lease->property;
        $owner    = $property?->owner;
        $mandat   = $property?->managementContract;
        $tenant   = $lease->tenant;
        $agency   = $lease->agency;

        $rent        = (float) $lease->rent_amount;
        $charges     = (float) ($lease->charges_amount ?? 0);
        $total       = $rent + $charges;
        $deposit     = (float) $lease->deposit_amount;
        $depositMonths = ($rent > 0) ? (int) round($deposit / $rent) : 2;

        $replacements = [
            // Locataire
            '{locataire_nom}'           => $tenant?->last_name ?? '',
            '{locataire_prenom}'        => $tenant?->first_name ?? '',
            '{locataire_nom_complet}'   => $tenant?->full_name ?? '',
            '{locataire_telephone}'     => $tenant?->phone ?? '',
            '{locataire_email}'         => $tenant?->email ?? '',
            '{locataire_piece_identite}'=> $tenant?->id_card_number ?? $tenant?->identity_document ?? 'CNIB N° non spécifiée',
            '{locataire_profession}'    => $tenant?->profession ?? 'N/A',
            '{locataire_nationalite}'   => $tenant?->nationality ?? 'Burkinabè',

            // Bien Immobilier
            '{bien_titre}'              => $property?->title ?? '',
            '{bien_reference}'          => $property?->reference ?? '',
            '{bien_adresse}'            => $property?->address ?? '',
            '{bien_ville}'              => $property?->city ?? 'Ouagadougou',
            '{bien_quartier}'           => $property?->neighborhood ?? '',

            // Propriétaire / Bailleur
            '{proprietaire_nom_complet}' => $owner?->full_name ?? '',
            '{proprietaire_telephone}'   => $owner?->phone ?? '',
            '{proprietaire_email}'       => $owner?->email ?? '',
            '{proprietaire_piece_identite}' => $owner?->id_card_number ?? $owner?->identity_document ?? 'N/A',
            '{proprietaire_profession}'  => $owner?->profession ?? 'Propriétaire bailleur',
            '{proprietaire_nationalite}' => $owner?->nationality ?? 'Burkinabè',

            // Mandat de gestion
            '{mandat_reference}'        => $mandat?->reference ?? 'En date',
            '{mandat_date}'             => $mandat?->start_date?->format('d/m/Y') ?? now()->subMonths(3)->format('d/m/Y'),

            // Loyer & Conditions financières
            '{loyer_montant}'           => number_format($rent, 0, ',', ' ') . ' FCFA',
            '{charges_montant}'         => number_format($charges, 0, ',', ' ') . ' FCFA',
            '{total_montant}'           => number_format($total, 0, ',', ' ') . ' FCFA',
            '{caution_montant}'         => number_format($deposit, 0, ',', ' ') . ' FCFA',
            '{caution_mois}'            => (string) $depositMonths,
            '{avance_mois}'             => '2',
            '{total_entree_montant}'    => number_format(($rent * 2) + $deposit, 0, ',', ' ') . ' FCFA',

            // Dates & Durée
            '{date_debut}'              => $lease->start_date?->format('d/m/Y') ?? '',
            '{date_fin}'                => $lease->end_date?->format('d/m/Y') ?? 'Indéterminée',
            '{duree_mois}'              => (string) ($lease->duration_months ?? 12),
            '{jour_echeance}'           => (string) ($lease->payment_due_day ?? 5),

            // Agence & Représentant / Gérant
            '{agence_nom}'              => $agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_legal_name}'       => $agency?->legal_name ?? $agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_phone}'            => $agency?->phone ?? '',
            '{agence_email}'            => $agency?->email ?? '',
            '{agence_adresse}'          => $agency?->address ?? '',
            '{agence_nif_rccm}'         => $agency?->nif_rccm ?? '',
            '{agence_gerant}'           => $agency?->manager_name ?? 'CONGO ERIC AMED WENDKUNI',
            '{agence_gerant_titre}'     => $agency?->manager_title ?? 'Gérant',
            '{agence_gerant_phone}'     => $agency?->manager_phone ?? $agency?->phone ?? '',
            '{agence_gerant_piece}'     => $agency?->manager_id_card ?? 'CNIB N°B15795168 du 03/06/2021 par ONI/Ouaga',
            '{date_du_jour}'            => now()->format('d/m/Y'),
        ];

        return strtr($templateContent, $replacements);
    }

    /**
     * Remplace les variables dynamiques dans un mandat de gestion.
     */
    public function replaceForManagement(string $templateContent, Owner $owner, ?Property $property = null, float $feePercentage = 10.0): string
    {
        $replacements = [
            '{proprietaire_nom}'         => $owner->last_name ?? '',
            '{proprietaire_prenom}'      => $owner->first_name ?? '',
            '{proprietaire_nom_complet}'   => $owner->full_name ?? '',
            '{proprietaire_telephone}'   => $owner->phone ?? '',
            '{proprietaire_email}'       => $owner->email ?? '',
            '{proprietaire_piece_identite}' => $owner->id_card_number ?? $owner->identity_document ?? 'N/A',
            '{proprietaire_profession}'  => $owner->profession ?? 'Propriétaire',
            '{proprietaire_nationalite}' => $owner->nationality ?? 'Burkinabè',
            '{bien_titre}'               => $property?->title ?? 'Tous les biens du propriétaire',
            '{bien_adresse}'             => $property?->address ?? 'N/A',
            '{bien_ville}'               => $property?->city ?? 'N/A',
            '{commission_pourcentage}'   => number_format($feePercentage, 1, ',', ' ') . ' %',
            '{agence_nom}'               => $owner->agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_telephone}'         => $owner->agency?->phone ?? '',
            '{agence_email}'             => $owner->agency?->email ?? '',
            '{agence_nif_rccm}'          => $owner->agency?->nif_rccm ?? '',
            '{agence_gerant}'           => $owner->agency?->manager_name ?? 'Le Gérant',
            '{agence_gerant_titre}'     => $owner->agency?->manager_title ?? 'Gérant',
            '{date_du_jour}'             => now()->format('d/m/Y'),
        ];

        return strtr($templateContent, $replacements);
    }

    /**
     * Liste des variables disponibles pour l'aide et la documentation des gabarits.
     */
    public static function getAvailableVariables(): array
    {
        return [
            'Locataire' => [
                '{locataire_nom_complet}'   => 'Nom et prénom du locataire',
                '{locataire_telephone}'     => 'Numéro de téléphone du locataire',
                '{locataire_email}'         => 'Adresse email du locataire',
                '{locataire_piece_identite}'=> 'Numéro CNI, passeport ou détails d\'identité',
                '{locataire_profession}'    => 'Profession du locataire',
                '{locataire_nationalite}'   => 'Nationalité du locataire',
            ],
            'Propriétaire / Bailleur' => [
                '{proprietaire_nom_complet}'=> 'Nom complet du bailleur',
                '{proprietaire_telephone}'  => 'Téléphone du bailleur',
                '{proprietaire_email}'      => 'Email du bailleur',
                '{proprietaire_piece_identite}' => 'Pièce d\'identité du bailleur',
                '{proprietaire_profession}' => 'Profession du bailleur',
                '{proprietaire_nationalite}' => 'Nationalité du bailleur',
            ],
            'Bien & Mandat' => [
                '{bien_titre}'              => 'Désignation / Titre du bien loué',
                '{bien_adresse}'            => 'Adresse exacte du bien (parcelle, lot)',
                '{bien_quartier}'           => 'Quartier du bien',
                '{bien_ville}'              => 'Ville du bien',
                '{mandat_reference}'        => 'Référence du mandat de gestion',
                '{mandat_date}'             => 'Date de signature du mandat',
            ],
            'Financier & Conditions' => [
                '{loyer_montant}'           => 'Montant du loyer mensuel en FCFA',
                '{charges_montant}'         => 'Montant des charges mensuelles',
                '{caution_montant}'         => 'Montant total du dépôt de garantie',
                '{caution_mois}'            => 'Nombre de mois de caution',
                '{avance_mois}'             => 'Nombre de mois d\'avance',
                '{total_entree_montant}'    => 'Somme totale perçue à l\'entrée (Avance + Caution)',
                '{date_debut}'              => 'Date de démarrage du bail',
                '{date_fin}'                => 'Date d\'échéance du bail',
                '{duree_mois}'              => 'Durée du bail en mois',
                '{jour_echeance}'           => 'Jour d\'échéance mensuelle du loyer',
            ],
            'Agence & Gérant' => [
                '{agence_nom}'              => 'Nom de l\'agence immobilière',
                '{agence_legal_name}'       => 'Raison sociale de l\'agence',
                '{agence_nif_rccm}'         => 'NIF / IFU / RCCM de l\'agence',
                '{agence_phone}'            => 'Téléphone de l\'agence',
                '{agence_adresse}'          => 'Adresse du siège social de l\'agence',
                '{agence_gerant}'           => 'Nom complet du Gérant / Responsable',
                '{agence_gerant_titre}'     => 'Titre / Qualité du responsable (ex: Gérant)',
                '{agence_gerant_phone}'     => 'Téléphone direct du responsable',
                '{agence_gerant_piece}'     => 'Pièce d\'identité du responsable',
                '{date_du_jour}'            => 'Date du jour de génération',
            ],
        ];
    }
}
