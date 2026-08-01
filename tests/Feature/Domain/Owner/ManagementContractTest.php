<?php

namespace Tests\Feature\Domain\Owner;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Services\ManagementContractGenerator;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManagementContractTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $user;
    protected Owner $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolesAndPermissions();

        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
        $this->user->assignRole('Administrateur');

        $this->owner = Owner::factory()->create(['agency_id' => $this->agency->id]);
    }

    public function test_can_create_management_contract_via_livewire(): void
    {
        $propertyType = PropertyType::factory()->create(['agency_id' => $this->agency->id]);
        $property = Property::factory()->create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $this->owner->id,
            'property_type_id' => $propertyType->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ManagementContracts\Create::class, ['ownerId' => $this->owner->id])
            ->set('reference', 'MAN-TEST-001')
            ->set('title', 'Mandat de Gestion Test')
            ->set('start_date', '2026-01-01')
            ->set('duration_months', 12)
            ->set('commission_type', 'percentage')
            ->set('commission_value', 10.0)
            ->set('selectedProperties', [$property->id])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('management_contracts', [
            'agency_id' => $this->agency->id,
            'owner_id'  => $this->owner->id,
            'reference' => 'MAN-TEST-001',
        ]);

        $contract = ManagementContract::where('reference', 'MAN-TEST-001')->first();
        $this->assertEquals($contract->id, $property->fresh()->management_contract_id);
    }

    public function test_owner_can_have_multiple_management_contracts(): void
    {
        $contract1 = ManagementContract::create([
            'agency_id'       => $this->agency->id,
            'owner_id'        => $this->owner->id,
            'reference'       => 'MAN-001',
            'title'           => 'Mandat 1',
            'start_date'      => '2025-01-01',
            'duration_months' => 12,
            'status'          => ManagementContractStatus::Active,
        ]);

        $contract2 = ManagementContract::create([
            'agency_id'       => $this->agency->id,
            'owner_id'        => $this->owner->id,
            'reference'       => 'MAN-002',
            'title'           => 'Mandat 2',
            'start_date'      => '2026-01-01',
            'duration_months' => 12,
            'status'          => ManagementContractStatus::Active,
        ]);

        $this->assertCount(2, $this->owner->managementContracts);
    }

    public function test_generator_produces_valid_contract_text(): void
    {
        $contract = ManagementContract::create([
            'agency_id'       => $this->agency->id,
            'owner_id'        => $this->owner->id,
            'reference'       => 'MAN-GEN-001',
            'title'           => 'Mandat Générateur',
            'start_date'      => '2026-01-01',
            'duration_months' => 12,
            'commission_type' => 'fixed',
            'commission_value'=> 15000,
            'status'          => ManagementContractStatus::Active,
        ]);

        $generator = new ManagementContractGenerator();
        $text = $generator->generateText($contract);

        $this->assertStringContainsString('MANDAT DE GESTION IMMOBILIÈRE', $text);
        $this->assertStringContainsString('MAN-GEN-001', $text);
        $this->assertStringContainsString('Article 1 : Objet du contrat', $text);
        $this->assertStringContainsString('Article 5 : Rémunération', $text);
    }

    public function test_can_access_management_contracts_index_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('management-contracts.index'))
            ->assertOk();
    }

    public function test_can_access_management_contract_print_page(): void
    {
        $contract = ManagementContract::create([
            'agency_id'       => $this->agency->id,
            'owner_id'        => $this->owner->id,
            'reference'       => 'MAN-PRINT-001',
            'title'           => 'Mandat Print',
            'start_date'      => '2026-01-01',
            'duration_months' => 12,
            'status'          => ManagementContractStatus::Active,
        ]);

        $this->actingAs($this->user)
            ->get(route('management-contracts.print', $contract->id))
            ->assertOk()
            ->assertSee('MANDAT DE GESTION IMMOBILIÈRE');
    }
}
