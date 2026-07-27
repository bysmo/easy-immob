<?php

namespace App\Domain\Lease\Services;

use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;

class TemplateVariableReplacer
{
    /**
     * Remplace les variables dynamiques dans un modèle de contrat de bail.
     */
    public function replaceForLease(string $templateContent, Lease $lease): string
    {
        $lease->loadMissing(['property.owner', 'tenant', 'agency']);

        $replacements = [
            '{locataire_nom}'          => $lease->tenant?->last_name ?? '',
            '{locataire_prenom}'       => $lease->tenant?->first_name ?? '',
            '{locataire_nom_complet}'  => $lease->tenant?->full_name ?? '',
            '{locataire_telephone}'    => $lease->tenant?->phone ?? '',
            '{locataire_email}'        => $lease->tenant?->email ?? '',
            '{locataire_piece_identite}' => $lease->tenant?->id_card_number ?? '',
            '{bien_titre}'             => $lease->property?->title ?? '',
            '{bien_reference}'         => $lease->property?->reference ?? '',
            '{bien_adresse}'           => $lease->property?->address ?? '',
            '{bien_ville}'             => $lease->property?->city ?? '',
            '{proprietaire_nom_complet}' => $lease->property?->owner?->full_name ?? '',
            '{proprietaire_telephone}'   => $lease->property?->owner?->phone ?? '',
            '{loyer_montant}'          => number_format((float) $lease->rent_amount, 0, ',', ' ') . ' FCFA',
            '{charges_montant}'        => number_format((float) ($lease->charges_amount ?? 0), 0, ',', ' ') . ' FCFA',
            '{caution_montant}'        => number_format((float) $lease->deposit_amount, 0, ',', ' ') . ' FCFA',
            '{date_debut}'             => $lease->start_date?->format('d/m/Y') ?? '',
            '{date_fin}'               => $lease->end_date?->format('d/m/Y') ?? 'Indéterminée',
            '{duree_mois}'             => $lease->duration_months ?? '12',
            '{agence_nom}'             => $lease->agency?->name ?? 'EasyImmob',
            '{date_du_jour}'           => now()->format('d/m/Y'),
        ];

        return strtr($templateContent, $replacements);
    }

    /**
     * Remplace les variables dynamiques dans un mandat de gestion pour propriétaire/bien.
     */
    public function replaceForManagement(string $templateContent, Owner $owner, ?Property $property = null, float $feePercentage = 8.0): string
    {
        $replacements = [
            '{proprietaire_nom}'       => $owner->last_name ?? '',
            '{proprietaire_prenom}'    => $owner->first_name ?? '',
            '{proprietaire_nom_complet}' => $owner->full_name ?? '',
            '{proprietaire_telephone}' => $owner->phone ?? '',
            '{proprietaire_email}'     => $owner->email ?? '',
            '{bien_titre}'             => $property?->title ?? 'Tous les biens du propriétaire',
            '{bien_adresse}'           => $property?->address ?? 'N/A',
            '{bien_ville}'             => $property?->city ?? 'N/A',
            '{commission_pourcentage}' => number_format($feePercentage, 1, ',', ' ') . ' %',
            '{agence_nom}'             => $owner->agency?->name ?? 'EasyImmob',
            '{date_du_jour}'           => now()->format('d/m/Y'),
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
                '{bien_titre}'              => 'Désignation du bien loué',
                '{bien_adresse}'            => 'Adresse exacte du bien',
                '{bien_ville}'              => 'Ville du bien',
                '{proprietaire_nom_complet}'=> 'Nom complet du bailleur',
                '{loyer_montant}'           => 'Montant du loyer en FCFA',
                '{caution_montant}'         => 'Montant du dépôt de garantie',
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
