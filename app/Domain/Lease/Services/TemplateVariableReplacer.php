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
            '{locataire_nom}'           => $tenant?->last_name ?? '',
            '{locataire_prenom}'        => $tenant?->first_name ?? '',
            '{locataire_nom_complet}'   => $tenant?->full_name ?? '',
            '{locataire_telephone}'     => $tenant?->phone ?? '',
            '{locataire_email}'         => $tenant?->email ?? '',
            '{locataire_piece_identite}'=> $tenant?->id_card_number ?? 'CNIB N° non spécifiée',
            '{locataire_profession}'    => $tenant?->profession ?? 'N/A',
            '{locataire_nationalite}'   => $tenant?->nationality ?? 'Burkinabè',
            
            '{bien_titre}'              => $property?->title ?? '',
            '{bien_reference}'          => $property?->reference ?? '',
            '{bien_adresse}'            => $property?->address ?? '',
            '{bien_ville}'              => $property?->city ?? 'Ouagadougou',
            '{bien_quartier}'           => $property?->neighborhood ?? '',
            
            '{proprietaire_nom_complet}' => $owner?->full_name ?? '',
            '{proprietaire_telephone}'   => $owner?->phone ?? '',
            '{proprietaire_email}'       => $owner?->email ?? '',
            '{proprietaire_piece_identite}' => $owner?->identity_document ?? 'N/A',
            '{proprietaire_profession}'  => 'Propriétaire bailleur',
            
            '{mandat_reference}'        => $mandat?->reference ?? 'En date',
            '{mandat_date}'             => $mandat?->start_date?->format('d/m/Y') ?? now()->subMonths(3)->format('d/m/Y'),
            
            '{loyer_montant}'           => number_format($rent, 0, ',', ' ') . ' FCFA',
            '{charges_montant}'         => number_format($charges, 0, ',', ' ') . ' FCFA',
            '{total_montant}'           => number_format($total, 0, ',', ' ') . ' FCFA',
            '{caution_montant}'         => number_format($deposit, 0, ',', ' ') . ' FCFA',
            '{caution_mois}'            => (string) $depositMonths,
            '{avance_mois}'             => '2',
            '{total_entree_montant}'    => number_format(($rent * 2) + $deposit, 0, ',', ' ') . ' FCFA',
            
            '{date_debut}'              => $lease->start_date?->format('d/m/Y') ?? '',
            '{date_fin}'                => $lease->end_date?->format('d/m/Y') ?? 'Indéterminée',
            '{duree_mois}'              => (string) ($lease->duration_months ?? 12),
            '{jour_echeance}'           => (string) ($lease->payment_due_day ?? 5),
            
            '{agence_nom}'              => $agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_legal_name}'       => $agency?->legal_name ?? $agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_phone}'            => $agency?->phone ?? '',
            '{agence_email}'            => $agency?->email ?? '',
            '{agence_adresse}'          => $agency?->address ?? '',
            '{agence_nif_rccm}'         => $agency?->nif_rccm ?? '',
            '{agence_gerant}'           => 'Le Gérant',
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
            '{proprietaire_piece_identite}' => $owner->identity_document ?? 'N/A',
            '{bien_titre}'               => $property?->title ?? 'Tous les biens du propriétaire',
            '{bien_adresse}'             => $property?->address ?? 'N/A',
            '{bien_ville}'               => $property?->city ?? 'N/A',
            '{commission_pourcentage}'   => number_format($feePercentage, 1, ',', ' ') . ' %',
            '{agence_nom}'               => $owner->agency?->name ?? 'KIPRESS ESTATE SARL',
            '{agence_telephone}'         => $owner->agency?->phone ?? '',
            '{agence_email}'             => $owner->agency?->email ?? '',
            '{agence_nif_rccm}'          => $owner->agency?->nif_rccm ?? '',
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
            'Bail Locatif' => [
                '{locataire_nom_complet}'   => 'Nom et prénom du locataire',
                '{locataire_telephone}'     => 'Numéro de téléphone du locataire',
                '{locataire_piece_identite}'=> 'Numéro CNI ou passeport',
                '{locataire_profession}'    => 'Profession du locataire',
                '{bien_titre}'              => 'Désignation du bien loué',
                '{bien_adresse}'            => 'Adresse exacte du bien',
                '{bien_ville}'              => 'Ville du bien',
                '{proprietaire_nom_complet}'=> 'Nom complet du bailleur',
                '{mandat_reference}'        => 'Référence du mandat de gestion',
                '{mandat_date}'             => 'Date du mandat de gestion',
                '{loyer_montant}'           => 'Montant du loyer en FCFA',
                '{caution_montant}'         => 'Montant du dépôt de garantie',
                '{caution_mois}'            => 'Nombre de mois de caution',
                '{total_entree_montant}'    => 'Somme totale perçue à l\'entrée',
                '{date_debut}'              => 'Date de démarrage du bail',
                '{date_fin}'                => 'Date d\'échéance du bail',
                '{agence_nom}'              => 'Nom de l\'agence mandataire',
                '{date_du_jour}'            => 'Date du jour de génération',
            ],
            'Mandat de Gestion' => [
                '{proprietaire_nom_complet}' => 'Nom du propriétaire mandant',
                '{proprietaire_telephone}'   => 'Téléphone du propriétaire',
                '{proprietaire_email}'       => 'Adresse e-mail du propriétaire',
                '{bien_titre}'               => 'Titre / désignation du bien',
                '{bien_adresse}'             => 'Adresse du bien géré',
                '{commission_pourcentage}'   => 'Taux d\'honoraires d\'agence (%)',
                '{agence_nom}'               => 'Nom de l\'agence mandataire',
                '{date_du_jour}'             => 'Date de signature',
            ],
        ];
    }
}
