<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'  => $agency->id,
            'arrears_id' => Arrear::factory()->for($agency, 'agency'),
            'channel'    => 'email',
            'sent_at'    => now(),
            'content'    => $this->faker->paragraph(),
            'status'     => 'sent',
        ];
    }
}
