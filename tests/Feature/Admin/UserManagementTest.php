<?php

namespace Tests\Feature\Admin;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Admin\Users\Create;
use App\Livewire\Admin\Users\Edit;
use App\Livewire\Admin\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    // ------------------------------------------------------------------
    // Accès et isolation par agence
    // ------------------------------------------------------------------

    public function test_admin_can_view_users_list(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->createAdminFor($agency);

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    public function test_user_without_permission_cannot_view_users_list(): void
    {
        $agency = Agency::factory()->create();
        $agent  = $this->createUserWithRole($agency, 'Agent');

        $this->actingAs($agent)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_users_list_is_scoped_to_current_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $adminA = $this->createAdminFor($agencyA);
        $userB  = User::factory()->for($agencyB, 'agency')->create(['name' => 'Utilisateur Agence B']);

        Livewire::actingAs($adminA)->test(Index::class)
            ->assertDontSee('Utilisateur Agence B');
    }

    // ------------------------------------------------------------------
    // Création
    // ------------------------------------------------------------------

    public function test_admin_can_create_a_user_in_the_same_agency(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->createAdminFor($agency);

        $this->assertDatabaseCount('users', 1);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Marie Dupont')
            ->set('email', 'marie@agence.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->set('role', 'Gestionnaire')
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseCount('users', 2);

        $newUser = User::withoutGlobalScopes()->where('email', 'marie@agence.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals($agency->id, $newUser->agency_id);
        $this->assertTrue($newUser->hasRole('Gestionnaire'));
    }

    public function test_create_user_fails_with_duplicate_email(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->createAdminFor($agency);

        User::factory()->for($agency, 'agency')->create(['email' => 'exists@agence.com']);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Autre')
            ->set('email', 'exists@agence.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->set('role', 'Gestionnaire')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    // ------------------------------------------------------------------
    // Édition
    // ------------------------------------------------------------------

    public function test_admin_can_edit_a_user_in_the_same_agency(): void
    {
        $agency  = Agency::factory()->create();
        $admin   = $this->createAdminFor($agency);
        $target  = $this->createUserWithRole($agency, 'Gestionnaire', 'Ancien Nom');

        Livewire::actingAs($admin)
            ->test(Edit::class, ['userId' => $target->id])
            ->set('name', 'Nouveau Nom')
            ->set('role', 'Comptable')
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();
        $this->assertEquals('Nouveau Nom', $target->name);
        $this->assertTrue($target->hasRole('Comptable'));
    }

    public function test_admin_cannot_edit_user_from_another_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $adminA = $this->createAdminFor($agencyA);
        $userB  = $this->createUserWithRole($agencyB, 'Gestionnaire');

        // La logique d'isolation est dans le mount() du composant Livewire.
        // On le teste unitairement en vérifiant que le composant mount
        // instancie bien un User nul (null) et non l'user d'une autre agence.
        $component = Livewire::actingAs($adminA)
            ->test(Edit::class, ['userId' => $userB->id]);

        // Livewire intercepte abort() : la propriété user doit rester null
        // car abort() coupe le mount avant l'assignation.
        $this->assertNull($component->get('user'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function createAdminFor(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password1'),
        ]);
        $user->assignRole('Administrateur');

        return $user;
    }

    private function createUserWithRole(Agency $agency, string $role, string $name = 'Utilisateur'): User
    {
        $user = User::factory()->for($agency, 'agency')->create([
            'name'     => $name,
            'password' => Hash::make('Password1'),
        ]);
        $user->assignRole($role);

        return $user;
    }
}
