<?php

namespace Tests\Feature\Domain\Property;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Livewire\Properties\Create;
use App\Livewire\Properties\Edit;
use App\Livewire\Properties\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_can_view_properties_list(): void
    {
        $agency   = Agency::factory()->create();
        $user     = $this->createAuthorizedUser($agency);

        Property::factory()->for($agency, 'agency')->create(['title' => 'Villa Test']);

        Livewire::actingAs($user)->test(Index::class)
            ->assertSee('Villa Test');
    }

    public function test_user_can_create_property_with_reference(): void
    {
        $agency       = Agency::factory()->create();
        $user         = $this->createAuthorizedUser($agency);
        $owner        = Owner::factory()->for($agency, 'agency')->create();
        $propertyType = PropertyType::factory()->for($agency, 'agency')->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Bel Appartement')
            ->set('owner_id', $owner->id)
            ->set('property_type_id', $propertyType->id)
            ->set('city', 'Abidjan')
            ->set('address', 'Cocody')
            ->set('rent_amount', 200000)
            ->set('status', 'available')
            ->call('save')
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'agency_id'   => $agency->id,
            'reference'   => 'BIE-0001',
            'title'       => 'Bel Appartement',
            'rent_amount' => 200000,
            'status'      => PropertyStatus::Available->value,
        ]);
    }

    public function test_user_can_edit_property(): void
    {
        $agency   = Agency::factory()->create();
        $user     = $this->createAuthorizedUser($agency);
        $property = Property::factory()->for($agency, 'agency')->create(['title' => 'Ancien Titre']);

        Livewire::actingAs($user)
            ->test(Edit::class, ['propertyId' => $property->id])
            ->set('title', 'Nouveau Titre')
            ->call('save')
            ->assertRedirect(route('properties.index'));

        $this->assertEquals('Nouveau Titre', $property->fresh()->title);
    }

    public function test_property_is_scoped_to_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $userA     = $this->createAuthorizedUser($agencyA);
        $propertyB = Property::factory()->for($agencyB, 'agency')->create(['title' => 'PropertyB']);

        Livewire::actingAs($userA)->test(Index::class)
            ->assertDontSee('PropertyB');
    }

    private function createAuthorizedUser(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Administrateur');

        return $user;
    }
}
