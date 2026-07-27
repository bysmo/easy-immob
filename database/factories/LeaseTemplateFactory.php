<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\LeaseTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseTemplate>
 */
class LeaseTemplateFactory extends Factory
{
    protected $model = LeaseTemplate::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'   => $agency->id,
            'name'        => 'Contrat d\'habitation standard',
            'description' => 'Modèle type pour la location à usage d\'habitation.',
            'content'     => "CONTRAT DE LOCATION\n\nEntre le bailleur {{owner_name}} et le locataire {{tenant_name}}.\nAdresse du bien : {{property_address}}.\nLoyer mensuel : {{rent_amount}} (charges : {{charges_amount}}).\nTotal mensuel : {{total_amount}}.\nCaution versée : {{deposit_amount}}.\nDurée : du {{start_date}} au {{end_date}}.\nPaiement attendu chaque {{payment_due}} du mois.\n\nFait le {{start_date}} à Abidjan.",
            'version'     => 1,
            'status'      => 'active',
        ];
    }
}
