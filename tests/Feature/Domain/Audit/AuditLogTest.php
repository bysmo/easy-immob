<?php

namespace Tests\Feature\Domain\Audit;

use App\Domain\Agency\Models\Agency;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Property\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_audit_logger_creates_log_entry(): void
    {
        $agency   = Agency::factory()->create();
        $user     = User::factory()->for($agency, 'agency')->create();
        $property = Property::factory()->for($agency, 'agency')->create();

        $this->actingAs($user);

        AuditLogger::log(
            action: 'updated',
            model: $property,
            oldValues: ['status' => 'available'],
            newValues: ['status' => 'occupied']
        );

        $this->assertDatabaseHas('audit_logs', [
            'agency_id'   => $agency->id,
            'user_id'     => $user->id,
            'action'      => 'updated',
            'entity_type' => Property::class,
            'entity_id'   => $property->id,
        ]);
    }
}
