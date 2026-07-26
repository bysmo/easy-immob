<?php

namespace Tests\Feature\Application\Actions\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
}
