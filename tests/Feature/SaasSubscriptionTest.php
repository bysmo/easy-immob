<?php

namespace Tests\Feature;

use App\Application\Actions\Auth\RegisterAgencyAction;
use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaasSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_super_admin_can_access_saas_dashboard(): void
    {
        $superAdmin = User::role('Super Admin')->first();

        $response = $this->actingAs($superAdmin)->get(route('admin.saas-dashboard'));
        $response->assertStatus(200);
    }

    public function test_agency_admin_cannot_access_saas_dashboard(): void
    {
        $agencyAdmin = User::role('Administrateur')->whereNotNull('agency_id')->first();

        $response = $this->actingAs($agencyAdmin)->get(route('admin.saas-dashboard'));
        $response->assertStatus(403);
    }

    public function test_agency_registration_grants_3_month_free_trial_and_notifies_super_admin(): void
    {
        $action = new RegisterAgencyAction();
        $user = $action->create([
            'agency_name'           => 'Nouvelle Agence Test',
            'name'                  => 'Directeur Test',
            'email'                 => 'directeur@agencetest.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $agency = $user->agency;

        $this->assertEquals('trialing', $agency->subscription_status);
        $this->assertNotNull($agency->trial_ends_at);
        $this->assertTrue($agency->trial_ends_at->isFuture());

        // Facture d'essai gratuit à 0 FCFA créée
        $this->assertDatabaseHas('saas_invoices', [
            'agency_id'    => $agency->id,
            'total_amount' => 0,
        ]);

        // Notification créée pour le Super Admin
        $this->assertDatabaseHas('notifications', [
            'type'    => 'agency_registered',
            'subject' => "Nouvelle Agence Inscrite : Nouvelle Agence Test",
        ]);
    }

    public function test_agency_admin_can_access_subscription_page(): void
    {
        $agencyAdmin = User::role('Administrateur')->whereNotNull('agency_id')->first();

        $response = $this->actingAs($agencyAdmin)->get(route('subscription.index'));
        $response->assertStatus(200);
        $response->assertSee('Mon Abonnement EasyImmob');
    }

    public function test_agency_cannot_downgrade_to_free_trial_plan(): void
    {
        $agencyAdmin = User::role('Administrateur')->whereNotNull('agency_id')->first();
        $freePlan = SubscriptionPlan::where('slug', 'essai-gratuit')->first();

        Livewire::actingAs($agencyAdmin)
            ->test(\App\Livewire\Subscription\Index::class)
            ->call('requestPlanChange', $freePlan->id)
            ->assertSet('showErrorModal', true)
            ->assertSee("L'offre Essai Gratuit (3 mois) est exclusivement attribuée lors de la création initiale");
    }

    public function test_agency_cannot_downgrade_if_property_count_exceeds_new_plan_quota(): void
    {
        $agencyAdmin = User::role('Administrateur')->whereNotNull('agency_id')->first();
        $agency = $agencyAdmin->agency;

        $starterPlan = SubscriptionPlan::where('slug', 'starter')->first(); // Limit 10 properties

        $owner = Owner::firstOrCreate(['agency_id' => $agency->id], [
            'first_name' => 'Jean', 'last_name' => 'Dupont', 'email' => 'jean@test.com', 'phone' => '0102030405',
        ]);

        // Create 12 properties for this agency so it exceeds Starter quota (10)
        for ($i = $agency->properties()->count(); $i < 12; $i++) {
            Property::create([
                'agency_id'       => $agency->id,
                'owner_id'        => $owner->id,
                'name'            => "Bien Test {$i}",
                'type'            => 'appartement',
                'address'         => '123 Rue Test',
                'rent_amount'     => 100000,
                'security_deposit' => 200000,
            ]);
        }

        Livewire::actingAs($agencyAdmin)
            ->test(\App\Livewire\Subscription\Index::class)
            ->call('requestPlanChange', $starterPlan->id)
            ->assertSet('showErrorModal', true)
            ->assertSee("ce qui dépasse le quota maximal de 10 bien(s)");
    }

    public function test_agency_can_upgrade_plan_with_modal_confirmation(): void
    {
        $agencyAdmin = User::role('Administrateur')->whereNotNull('agency_id')->first();
        $agency = $agencyAdmin->agency;
        $proPlan = SubscriptionPlan::where('slug', 'pro-business')->first();

        Livewire::actingAs($agencyAdmin)
            ->test(\App\Livewire\Subscription\Index::class)
            ->call('requestPlanChange', $proPlan->id)
            ->assertSet('showConfirmModal', true)
            ->call('executePlanChange')
            ->assertSet('showConfirmModal', false);

        $this->assertEquals($proPlan->id, $agency->fresh()->subscription_plan_id);
    }
}
