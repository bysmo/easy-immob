<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
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

    public function test_a_successful_login_resets_the_rate_limiter(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        // 4 failed attempts — one below the 5-attempt block threshold.
        for ($i = 0; $i < 4; $i++) {
            Livewire::test(Login::class)
                ->set('email', $user->email)
                ->set('password', 'wrong-password')
                ->call('authenticate')
                ->assertHasErrors(['email' => 'Identifiants incorrects.']);
        }

        // Succeed on the 5th attempt — this should call RateLimiter::clear().
        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        auth()->logout();

        // Two more failed attempts. The block check (tooManyAttempts) runs
        // BEFORE the hit that follows a failure, so a single post-success
        // failure would read as "wrong password" whether or not the counter
        // was reset (it's the hit that follows it that would push a
        // *leftover* count of 4 up to 5). It's the SECOND post-success
        // failure that exposes a broken reset: if the 4 pre-success failures
        // were never cleared, this second attempt sees a count of 5 already
        // and is blocked with the rate-limit message instead of the normal
        // wrong-password one.
        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'still-wrong')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'Identifiants incorrects.']);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'still-wrong-again')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'Identifiants incorrects.']);

        $this->assertGuest();
    }

    public function test_rate_limit_decays_after_sixty_seconds_allowing_login_again(): void
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

        $this->travel(61)->seconds();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_fortify_raw_login_route_returns_validation_error_instead_of_500(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_fortify_raw_login_route_is_rate_limited_after_five_attempts(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
        $this->assertGuest();
    }
}
