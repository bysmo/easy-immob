<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Audit\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'   => $agency->id,
            'user_id'     => User::factory()->for($agency, 'agency'),
            'action'      => 'updated',
            'entity_type' => 'App\Domain\Lease\Models\Lease',
            'entity_id'   => 1,
            'old_values'  => ['status' => 'draft'],
            'new_values'  => ['status' => 'active'],
            'ip_address'  => '127.0.0.1',
            'user_agent'  => 'PHPUnit Test',
        ];
    }
}
