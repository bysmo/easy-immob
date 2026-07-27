<?php

namespace Tests\Feature\Domain\Arrears;

use App\Domain\Agency\Models\Agency;
use App\Domain\Arrears\Actions\SendReminderAction;
use App\Domain\Arrears\Models\Arrear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_send_reminder_action_creates_reminder_and_notification(): void
    {
        $agency = Agency::factory()->create();
        $arrear = Arrear::factory()->for($agency, 'agency')->create();

        /** @var SendReminderAction $action */
        $action   = app(SendReminderAction::class);
        $reminder = $action->execute($arrear, 'email', 'Message de relance de test');

        $this->assertDatabaseHas('reminders', [
            'agency_id'  => $agency->id,
            'arrears_id' => $arrear->id,
            'channel'    => 'email',
            'content'    => 'Message de relance de test',
        ]);

        $this->assertDatabaseHas('notifications', [
            'agency_id'      => $agency->id,
            'recipient_type' => get_class($arrear->tenant),
            'recipient_id'   => $arrear->tenant_id,
            'channel'        => 'email',
        ]);
    }
}
