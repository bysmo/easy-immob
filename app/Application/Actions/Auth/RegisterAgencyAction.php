<?php

namespace App\Application\Actions\Auth;

use App\Domain\Agency\Models\Agency;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisterAgencyAction implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered agency with a 3-month free trial and notify Super Admin.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'agency_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('agencies', 'email'),
            ],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ])->validate();

        return DB::transaction(function () use ($input) {
            // Récupération ou création du plan d'essai gratuit sur 3 mois
            $freeTrialPlan = SubscriptionPlan::where('slug', 'essai-gratuit')->first()
                ?? SubscriptionPlan::first();

            $agency = Agency::create([
                'name'                 => $input['agency_name'],
                'email'                => $input['email'],
                'status'               => 'active',
                'subscription_plan_id' => $freeTrialPlan?->id,
                'billing_cycle'        => 'monthly',
                'subscription_status'  => 'trialing',
                'trial_ends_at'        => now()->addMonths(3),
                'subscription_ends_at' => now()->addMonths(3),
            ]);

            $user = User::create([
                'agency_id' => $agency->id,
                'name'      => $input['name'],
                'email'     => $input['email'],
                'password'  => Hash::make($input['password']),
            ]);

            $user->assignRole('Administrateur');

            // Génération de la facture initiale d'essai gratuit à 0 FCFA
            if ($freeTrialPlan) {
                SaasInvoice::create([
                    'number'               => SaasInvoice::generateNumber(),
                    'agency_id'            => $agency->id,
                    'subscription_plan_id' => $freeTrialPlan->id,
                    'billing_cycle'        => 'monthly',
                    'amount'               => 0,
                    'tax_amount'           => 0,
                    'total_amount'         => 0,
                    'status'               => 'paid',
                    'invoice_date'         => now(),
                    'due_date'             => now()->addMonths(3),
                    'paid_at'              => now(),
                    'payment_method'       => 'Offre d\'essai (Gratuit 3 mois)',
                    'notes'                => 'Période d\'essai gratuit de 3 mois offerte dès l\'inscription.',
                ]);
            }

            // Notification des Super Admins SaaS de la nouvelle inscription d'agence
            $superAdmins = User::role('Super Admin')->get();
            foreach ($superAdmins as $superAdmin) {
                SystemNotification::create([
                    'agency_id'      => $agency->id,
                    'recipient_type' => User::class,
                    'recipient_id'   => $superAdmin->id,
                    'type'           => 'agency_registered',
                    'channel'        => 'database',
                    'subject'        => "Nouvelle Agence Inscrite : {$agency->name}",
                    'content'        => "L'agence immobilière {$agency->name} ({$agency->email}) s'est inscrite sur la plateforme avec l'offre d'essai gratuit 3 mois.",
                    'sent_at'        => now(),
                    'status'         => 'sent',
                ]);
            }

            return $user;
        });
    }
}
