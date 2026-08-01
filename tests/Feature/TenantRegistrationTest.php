<?php

namespace Tests\Feature;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_citizen_can_register_as_tenant_and_get_unique_code(): void
    {
        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('account_type', 'tenant')
            ->set('first_name', 'Moussa')
            ->set('last_name', 'Ouédraogo')
            ->set('email', 'moussa.ouedraogo@example.com')
            ->set('phone', '+226 70 11 22 33')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('register');

        // Verify User was created
        $user = User::where('email', 'moussa.ouedraogo@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Locataire'));
        $this->assertNull($user->agency_id);

        // Verify Tenant was created
        $tenant = Tenant::where('user_id', $user->id)->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Moussa', $tenant->first_name);
        $this->assertEquals('Ouédraogo', $tenant->last_name);
        $this->assertStringStartsWith('LOC-', $tenant->reference);
    }
}
