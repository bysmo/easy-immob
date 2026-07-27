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

        foreach ($agencies as $agency) {
            LeaseTemplate::withoutGlobalScopes()->firstOrCreate([
                'agency_id' => $agency->id,
                'name'      => 'Contrat d\'habitation type (Bail à usage d\'habitation)',
            ], [
                'description' => 'Modèle officiel par défaut d\'un bail d\'habitation.',
                'content'     => "CONTRAT DE LOCATION À USAGE D'HABITATION\n\n".
                                 "Référence du bail : {{lease_reference}}\n\n".
                                 "ENTRE LES SOUSSIGNÉS :\n".
                                 "Le Bailleur : {{owner_name}}\n".
                                 "Et le Locataire : {{tenant_name}}\n\n".
                                 "DÉSIGNATION DES LIEUX :\n".
                                 "Le bailleur donne à bail au locataire les locaux situés à : {{property_address}}.\n\n".
                                 "CONDITIONS FINANCIÈRES :\n".
                                 "- Loyer mensuel principal : {{rent_amount}}\n".
                                 "- Provision pour charges : {{charges_amount}}\n".
                                 "- Total mensuel à payer : {{total_amount}}\n".
                                 "- Dépôt de garantie (caution) : {{deposit_amount}}\n\n".
                                 "MODALITÉS DE PAIEMENT :\n".
                                 "Le loyer est payable d'avance au plus tard le {{payment_due_day}} de chaque mois.\n\n".
                                 "DURÉE DU CONTRAT :\n".
                                 "Le présent contrat est conclu pour une durée déterminée allant du {{start_date}} au {{end_date}}.\n\n".
                                 "Fait en deux exemplaires originaux.",
                'version'     => 1,
                'status'      => 'active',
            ]);
        }
    }
}
