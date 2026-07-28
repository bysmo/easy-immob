<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Essai Gratuit (3 mois)',
                'slug' => 'essai-gratuit',
                'description' => 'Offre d\'essai complète de 3 mois offerte à toute nouvelle agence inscrite.',
                'max_properties' => 10,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    '3 mois d\'accès 100% gratuit',
                    'Gestion jusqu\'à 10 biens à louer',
                    'Baux & Quittancement automatisé',
                    'Suivi des loyers & impayés',
                    'Support client EasyImmob',
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Idéal pour les petites agences immobilières débutantes.',
                'max_properties' => 10,
                'price_monthly' => 25000,
                'price_yearly' => 250000, // 2 mois gratuits
                'features' => [
                    'Jusqu\'à 10 biens à louer',
                    'Gestion locative & baux basiques',
                    'Suivi des loyers & encaissements',
                    'Messagerie locataire',
                    'Support par email',
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Pro Business',
                'slug' => 'pro-business',
                'description' => 'La solution complète pour les agences en pleine croissance.',
                'max_properties' => 50,
                'price_monthly' => 65000,
                'price_yearly' => 650000, // 2 mois gratuits
                'features' => [
                    'Jusqu\'à 50 biens à louer',
                    'Gestion complète baux & quittancement',
                    'Relances impayés & alertes SMS/Email',
                    'Rapports financiers & relevés propriétaire',
                    'Export comptable CSV',
                    'Support prioritaire 7j/7',
                ],
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Pour les grandes agences et réseaux immobiliers d\'envergure.',
                'max_properties' => 999999, // Illimité
                'price_monthly' => 150000,
                'price_yearly' => 1500000,
                'features' => [
                    'Nombre de biens illimité',
                    'Toutes les fonctionnalités Pro Business',
                    'Multi-utilisateurs & rôles sur mesure',
                    'API & intégrations personnalisées',
                    'Account manager dédié',
                    'Sauvegarde & archivage haute sécurité',
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $proPlan = SubscriptionPlan::where('slug', 'pro-business')->first();
        $starterPlan = SubscriptionPlan::where('slug', 'starter')->first();

        // Associer les agences existantes à un plan si ce n'est pas fait
        $agencies = Agency::all();
        foreach ($agencies as $index => $agency) {
            $plan = ($index % 2 === 0) ? $proPlan : $starterPlan;
            $cycle = ($index % 2 === 0) ? 'yearly' : 'monthly';

            $agency->update([
                'subscription_plan_id' => $plan->id,
                'billing_cycle'        => $cycle,
                'subscription_status'  => 'active',
                'subscription_ends_at' => now()->addMonths($cycle === 'yearly' ? 12 : 1),
            ]);

            // Générer 2 factures d'exemple par agence
            if ($agency->saasInvoices()->count() === 0) {
                // Facture payée du mois/année passée
                SaasInvoice::create([
                    'number'               => SaasInvoice::generateNumber(),
                    'agency_id'            => $agency->id,
                    'subscription_plan_id' => $plan->id,
                    'billing_cycle'        => $cycle,
                    'amount'               => $plan->getPriceForCycle($cycle),
                    'tax_amount'           => 0,
                    'total_amount'         => $plan->getPriceForCycle($cycle),
                    'status'               => 'paid',
                    'invoice_date'         => now()->subMonths(1)->startOfMonth(),
                    'due_date'             => now()->subMonths(1)->startOfMonth()->addDays(10),
                    'paid_at'              => now()->subMonths(1)->startOfMonth()->addDays(2),
                    'payment_method'       => 'Mobile Money (Orange Money)',
                    'notes'                => 'Règlement effectué avec succès.',
                ]);

                // Facture récente
                SaasInvoice::create([
                    'number'               => SaasInvoice::generateNumber(),
                    'agency_id'            => $agency->id,
                    'subscription_plan_id' => $plan->id,
                    'billing_cycle'        => $cycle,
                    'amount'               => $plan->getPriceForCycle($cycle),
                    'tax_amount'           => 0,
                    'total_amount'         => $plan->getPriceForCycle($cycle),
                    'status'               => 'paid',
                    'invoice_date'         => now()->startOfMonth(),
                    'due_date'             => now()->startOfMonth()->addDays(10),
                    'paid_at'              => now()->startOfMonth()->addDays(1),
                    'payment_method'       => 'Virement Bancaire',
                    'notes'                => 'Facture acquittée.',
                ]);
            }
        }
    }
}
