<?php

namespace Tests\Feature\Support\Tenancy;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_scoped_to_the_authenticated_users_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $userA = User::factory()->for($agencyA, 'agency')->create();
        User::factory()->for($agencyB, 'agency')->create();

        $this->actingAs($userA);

        $this->assertCount(1, User::all());
        $this->assertTrue(User::first()->is($userA));
    }

    public function test_new_records_are_stamped_with_the_authenticated_users_agency(): void
    {
        $agency = Agency::factory()->create();
        $userA = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($userA);

        $created = User::create([
            'name' => 'Nouveau Collègue',
            'email' => 'collegue@example.com',
            'password' => 'password',
        ]);

        $this->assertSame($agency->id, $created->agency_id);
    }

    public function test_without_an_authenticated_user_no_scope_is_applied(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        User::factory()->for($agencyA, 'agency')->create();
        User::factory()->for($agencyB, 'agency')->create();

        $this->assertCount(2, User::all());
    }

    public function test_resolving_the_authenticated_user_from_the_session_does_not_recurse(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->session([
            'login_web_'.sha1(\Illuminate\Auth\SessionGuard::class) => $user->getAuthIdentifier(),
        ]);

        $resolved = $this->app['auth']->guard('web')->user();

        $this->assertNotNull($resolved);
        $this->assertTrue($resolved->is($user));
    }
}
