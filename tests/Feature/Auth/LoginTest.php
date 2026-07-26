<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_log_in_with_correct_credentials(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', $user->email)
                ->set('password', 'wrong-password')
                ->call('authenticate');
        }

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }
}
