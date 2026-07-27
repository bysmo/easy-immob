<?php

namespace Tests\Feature\Domain\Owner;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Livewire\Owners\Create;
use App\Livewire\Owners\Edit;
use App\Livewire\Owners\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_can_view_owners_list(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        Owner::factory()->for($agency, 'agency')->create(['first_name' => 'Jean', 'last_name' => 'Dupont']);

        Livewire::actingAs($user)->test(Index::class)
            ->assertSee('Dupont Jean');
    }

    public function test_user_can_create_owner_with_auto_generated_reference(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('first_name', 'Marc')
            ->set('last_name', 'Kouassi')
            ->set('email', 'marc@example.com')
            ->set('phone', '0102030405')
            ->call('save')
            ->assertRedirect(route('owners.index'));

        $this->assertDatabaseHas('owners', [
            'agency_id'  => $agency->id,
            'reference'  => 'PRO-0001',
            'first_name' => 'Marc',
            'last_name'  => 'Kouassi',
        ]);
    }

    public function test_user_can_edit_owner(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);
        $owner  = Owner::factory()->for($agency, 'agency')->create(['first_name' => 'Ancien']);

        Livewire::actingAs($user)
            ->test(Edit::class, ['ownerId' => $owner->id])
            ->set('first_name', 'Nouveau')
            ->call('save')
            ->assertRedirect(route('owners.index'));

        $this->assertEquals('Nouveau', $owner->fresh()->first_name);
    }

    public function test_user_can_soft_delete_owner(): void
    {
        $agency = Agency::factory()->create();
        $user   = $this->createAuthorizedUser($agency);
        $owner  = Owner::factory()->for($agency, 'agency')->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $owner->id);

        $this->assertSoftDeleted('owners', ['id' => $owner->id]);
    }

    public function test_owner_is_scoped_to_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $userA  = $this->createAuthorizedUser($agencyA);
        $ownerB = Owner::factory()->for($agencyB, 'agency')->create(['first_name' => 'OwnerB']);

        Livewire::actingAs($userA)->test(Index::class)
            ->assertDontSee('OwnerB');
    }

    private function createAuthorizedUser(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Administrateur');

        return $user;
    }
}
