<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_register_page_is_accessible_as_guest(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Register::class);
    }

    public function test_an_agency_and_admin_are_created_on_successful_registration(): void
    {
        $this->assertDatabaseCount('agencies', 0);
        $this->assertDatabaseCount('users', 0);

        Livewire::test(Register::class)
            ->set('agency_name', 'Agence Test')
            ->set('name', 'Jean Admin')
            ->set('email', 'jean@agence-test.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('agencies', 1);
        $this->assertDatabaseCount('users', 1);

        $this->assertDatabaseHas('agencies', ['name' => 'Agence Test']);
        $this->assertDatabaseHas('users', [
            'name'  => 'Jean Admin',
            'email' => 'jean@agence-test.com',
        ]);

        $user = User::withoutGlobalScopes()->first();
        $this->assertTrue($user->hasRole('Administrateur'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        $agency = Agency::factory()->create(['email' => 'taken@example.com']);
        User::factory()->for($agency, 'agency')->create(['email' => 'taken@example.com']);

        Livewire::test(Register::class)
            ->set('agency_name', 'Autre Agence')
            ->set('name', 'Autre Admin')
            ->set('email', 'taken@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_registration_fails_with_mismatched_passwords(): void
    {
        Livewire::test(Register::class)
            ->set('agency_name', 'Mon Agence')
            ->set('name', 'Admin')
            ->set('email', 'admin@test.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'DifferentPassword1')
            ->call('register')
            ->assertHasErrors(['password_confirmation']);
    }

    public function test_registration_requires_all_fields(): void
    {
        Livewire::test(Register::class)
            ->call('register')
            ->assertHasErrors(['agency_name', 'name', 'email', 'password']);
    }

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $agency = Agency::factory()->create();
        $user   = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)->get('/register')->assertRedirect(route('dashboard'));
    }
}
