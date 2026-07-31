<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Actions\SettleOwnerPayoutAction;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Owner\Services\OwnerPayoutCalculatorService;
use App\Domain\Property\Models\Property;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerPayoutTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $user;
    protected Owner $owner;
    protected Property $property;
    protected Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::create([
            'name'            => 'Agence Test Payout',
            'legal_name'      => 'Agence Test SARL',
            'email'           => 'payout@agence.com',
            'phone'           => '+22670000000',
            'commission_rate' => 10.0,
        ]);

        $this->user = User::factory()->create([
            'agency_id' => $this->agency->id,
        ]);

        $this->owner = Owner::create([
            'agency_id'  => $this->agency->id,
            'reference'  => 'BAIL-001',
            'first_name' => 'Jean',
            'last_name'  => 'Kaboré',
            'email'      => 'jean@kabore.com',
            'phone'      => '+22670112233',
            'status'     => 'active',
        ]);

        $propertyType = \App\Domain\Property\Models\PropertyType::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Villa',
        ]);

        $this->property = Property::create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $this->owner->id,
            'property_type_id' => $propertyType->id,
            'reference'        => 'BIEN-001',
            'title'            => 'Villa F4 Ouaga 2000',
            'address'          => 'Avenue Pascal',
            'city'             => 'Ouagadougou',
            'neighborhood'     => 'Ouaga 2000',
            'rent_amount'      => 200000,
            'agency_fee_type'  => 'percentage',
            'agency_fee_value' => 10.0,
            'status'           => 'occupied',
        ]);

        $tenant = Tenant::create([
            'agency_id'  => $this->agency->id,
            'reference'  => 'LOC-001',
            'first_name' => 'Paul',
            'last_name'  => 'Zongo',
            'status'     => 'active',
        ]);

        $this->lease = Lease::create([
            'agency_id'        => $this->agency->id,
            'reference'        => 'BAIL-2026-01',
            'property_id'      => $this->property->id,
            'tenant_id'        => $tenant->id,
            'rent_amount'      => 200000,
            'start_date'       => '2026-01-01',
            'end_date'         => '2026-12-31',
            'status'           => 'active',
        ]);
    }

    public function test_calculate_payout_for_collected_rents(): void
    {
        // Création d'une échéance payée partiellement ou totalement
        RentSchedule::create([
            'agency_id'        => $this->agency->id,
            'lease_id'         => $this->lease->id,
            'period'           => '2026-07',
            'due_date'         => '2026-07-05',
            'expected_amount'  => 200000,
            'paid_amount'      => 200000,
            'remaining_amount' => 0,
            'status'           => 'paid',
        ]);

        $service = new OwnerPayoutCalculatorService();
        $payouts = $service->calculateAndGeneratePayouts(
            agency: $this->agency,
            period: '2026-07',
            calculationType: 'collected',
            ownerId: $this->owner->id,
            creator: $this->user
        );

        $this->assertCount(1, $payouts);

        /** @var OwnerPayout $payout */
        $payout = $payouts->first();
        $this->assertEquals($this->owner->id, $payout->owner_id);
        $this->assertEquals(200000, $payout->gross_amount);
        $this->assertEquals(20000, $payout->commission_amount); // 10% de 200k
        $this->assertEquals(180000, $payout->net_amount); // 200k - 20k
        $this->assertEquals('pending', $payout->status->value);
    }

    public function test_calculate_payout_for_expected_rents_even_if_not_paid(): void
    {
        // Échéance non encore payée par le locataire
        RentSchedule::create([
            'agency_id'        => $this->agency->id,
            'lease_id'         => $this->lease->id,
            'period'           => '2026-08',
            'due_date'         => '2026-08-05',
            'expected_amount'  => 200000,
            'paid_amount'      => 0,
            'remaining_amount' => 200000,
            'status'           => 'pending',
        ]);

        $service = new OwnerPayoutCalculatorService();
        $payouts = $service->calculateAndGeneratePayouts(
            agency: $this->agency,
            period: '2026-08',
            calculationType: 'expected',
            ownerId: $this->owner->id,
            creator: $this->user
        );

        $this->assertCount(1, $payouts);
        $payout = $payouts->first();
        $this->assertEquals(200000, $payout->gross_amount);
        $this->assertEquals(180000, $payout->net_amount);
    }

    public function test_settle_owner_payout_with_proof_attachment(): void
    {
        Storage::fake('public');

        RentSchedule::create([
            'agency_id'        => $this->agency->id,
            'lease_id'         => $this->lease->id,
            'period'           => '2026-07',
            'due_date'         => '2026-07-05',
            'expected_amount'  => 200000,
            'paid_amount'      => 200000,
            'remaining_amount' => 0,
            'status'           => 'paid',
        ]);

        $service = new OwnerPayoutCalculatorService();
        $payouts = $service->calculateAndGeneratePayouts($this->agency, '2026-07', 'collected', $this->owner->id, $this->user);
        $payout = $payouts->first();

        $action = new SettleOwnerPayoutAction();
        $proofFile = UploadedFile::fake()->image('recu_mobile_money.jpg');

        $settlement = $action->execute($payout, [
            'payment_date'          => '2026-07-10',
            'amount'                => 180000,
            'payment_method'        => 'mobile_money',
            'transaction_reference' => 'TX-9988223311',
            'proof_document'        => $proofFile,
            'notes'                 => 'Paiement effectué via Orange Money',
        ], $this->user);

        $payout->refresh();
        $this->assertEquals(180000, $payout->paid_amount);
        $this->assertEquals('paid', $payout->status->value);
        $this->assertNotNull($settlement->proof_document_path);
        Storage::disk('public')->assertExists($settlement->proof_document_path);
    }

    public function test_payouts_index_livewire_component(): void
    {
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Owners\PayoutsIndex::class)
            ->set('calcPeriod', '2026-07')
            ->set('calcType', 'collected')
            ->call('runCalculation')
            ->assertStatus(200);
    }
}
