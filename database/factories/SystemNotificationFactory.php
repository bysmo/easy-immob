<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SystemNotification>
 */
class SystemNotificationFactory extends Factory
{
    protected $model = SystemNotification::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();
        $tenant = Tenant::factory()->for($agency, 'agency')->create();

        return [
            'agency_id'      => $agency->id,
            'recipient_type' => Tenant::class,
            'recipient_id'   => $tenant->id,
            'type'           => 'arrear_reminder',
            'channel'        => 'email',
            'subject'        => 'Rappel de loyer',
            'content'        => $this->faker->sentence(),
            'sent_at'        => now(),
            'status'         => 'sent',
        ];
    }
}
