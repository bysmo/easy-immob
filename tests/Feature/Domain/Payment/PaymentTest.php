<?php

namespace Tests\Feature\Domain\Payment;

use App\Domain\Agency\Models\Agency;
use App\Domain\Payment\Actions\RecordPaymentAction;
use App\Domain\Payment\Models\Payment;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_recording_full_payment_marks_schedule_as_paid(): void
    {
        $agency   = Agency::factory()->create();
        $user     = $this->createAuthorizedUser($agency);
        $schedule = RentSchedule::factory()->for($agency, 'agency')->create([
            'expected_amount'  => 150000,
            'paid_amount'      => 0,
            'remaining_amount' => 150000,
            'status'           => RentScheduleStatus::Pending,
        ]);

        $this->actingAs($user);

        /** @var RecordPaymentAction $action */
        $action  = app(RecordPaymentAction::class);
        $payment = $action->execute(
            schedule: $schedule,
            amount: 150000,
            paymentDate: '2026-08-05',
            paymentMethod: 'cash'
        );

        $this->assertDatabaseHas('payments', [
            'agency_id'        => $agency->id,
            'rent_schedule_id' => $schedule->id,
            'amount'           => 150000,
            'reference'        => 'PAY-0001',
        ]);

        $schedule->refresh();
        $this->assertEquals(150000, (float) $schedule->paid_amount);
        $this->assertEquals(0, (float) $schedule->remaining_amount);
        $this->assertEquals(RentScheduleStatus::Paid, $schedule->status);
    }

    public function test_recording_partial_payment_marks_schedule_as_partially_paid(): void
    {
        $agency   = Agency::factory()->create();
        $user     = $this->createAuthorizedUser($agency);
        $schedule = RentSchedule::factory()->for($agency, 'agency')->create([
            'expected_amount'  => 200000,
            'paid_amount'      => 0,
            'remaining_amount' => 200000,
            'status'           => RentScheduleStatus::Pending,
        ]);

        $this->actingAs($user);

        /** @var RecordPaymentAction $action */
        $action = app(RecordPaymentAction::class);
        $action->execute(
            schedule: $schedule,
            amount: 100000,
            paymentDate: '2026-08-05'
        );

        $schedule->refresh();
        $this->assertEquals(100000, (float) $schedule->paid_amount);
        $this->assertEquals(100000, (float) $schedule->remaining_amount);
        $this->assertEquals(RentScheduleStatus::PartiallyPaid, $schedule->status);
    }

    private function createAuthorizedUser(Agency $agency): User
    {
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Administrateur');

        return $user;
    }
}
