<?php

namespace Tests\Feature\Application\Actions\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Tests\TestCase;

class RegisterAgencyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_agency_and_its_first_administrator(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = (new RegisterAgencyAction)->create([
            'agency_name' => 'Agence du Plateau',
            'name' => 'Awa Konan',
            'email' => 'awa@plateau.example',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('agencies', ['name' => 'Agence du Plateau']);
        $this->assertSame($user->agency_id, $user->fresh()->agency->id);
        $this->assertTrue($user->fresh()->hasRole('Administrateur'));
    }

    public function test_it_rejects_a_weak_password(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->expectException(ValidationException::class);

        try {
            (new RegisterAgencyAction)->create([
                'agency_name' => 'Agence du Plateau',
                'name' => 'Awa Konan',
                'email' => 'awa@plateau.example',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);
        } finally {
            $this->assertDatabaseMissing('agencies', ['name' => 'Agence du Plateau']);
            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_it_rejects_an_email_already_used_by_an_existing_agency_even_without_a_matching_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        // Simulates a freed-up email: an agency row whose email survives even though
        // no user with that email exists anymore (e.g. after a future user-deletion feature).
        Agency::factory()->create(['email' => 'orphaned@plateau.example']);

        $this->expectException(ValidationException::class);

        try {
            (new RegisterAgencyAction)->create([
                'agency_name' => 'Agence du Plateau 2',
                'name' => 'Awa Konan',
                'email' => 'orphaned@plateau.example',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);
        } finally {
            $this->assertDatabaseMissing('agencies', ['name' => 'Agence du Plateau 2']);
            $this->assertDatabaseCount('agencies', 1);
            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_it_rolls_back_the_transaction_when_the_administrateur_role_does_not_exist(): void
    {
        // No RolesAndPermissionsSeeder run here: simulates `migrate` without `--seed`.
        $this->expectException(RoleDoesNotExist::class);

        try {
            (new RegisterAgencyAction)->create([
                'agency_name' => 'Agence du Plateau',
                'name' => 'Awa Konan',
                'email' => 'awa@plateau.example',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);
        } finally {
            $this->assertDatabaseCount('agencies', 0);
            $this->assertDatabaseCount('users', 0);
        }
    }
}
