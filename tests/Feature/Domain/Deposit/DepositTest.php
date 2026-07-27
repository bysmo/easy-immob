<?php

namespace Tests\Feature\Domain\Deposit;

use App\Domain\Agency\Models\Agency;
use App\Domain\Deposit\Actions\ReceiveDepositAction;
use App\Domain\Deposit\Actions\RefundDepositAction;
use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_can_receive_deposit(): void
    {
        $agency  = Agency::factory()->create();
        $deposit = Deposit::factory()->for($agency, 'agency')->create([
            'expected_amount' => 300000,
            'status'          => DepositStatus::Pending,
        ]);

        /** @var ReceiveDepositAction $action */
        $action = app(ReceiveDepositAction::class);
        $action->execute($deposit, 300000, '2026-08-01');

        $deposit->refresh();
        $this->assertEquals(300000, (float) $deposit->received_amount);
        $this->assertEquals(DepositStatus::Held, $deposit->status);
    }

    public function test_user_can_refund_deposit_with_partial_retention_reason(): void
    {
        $agency  = Agency::factory()->create();
        $deposit = Deposit::factory()->for($agency, 'agency')->held()->create([
            'received_amount' => 300000,
        ]);

        /** @var RefundDepositAction $action */
        $action = app(RefundDepositAction::class);
        $action->execute(
            deposit: $deposit,
            refundedAmount: 200000,
            retainedAmount: 100000,
            retentionReason: 'Dégradation peinture salon',
            refundedAt: '2027-08-01'
        );

        $deposit->refresh();
        $this->assertEquals(200000, (float) $deposit->refunded_amount);
        $this->assertEquals(100000, (float) $deposit->retained_amount);
        $this->assertEquals('Dégradation peinture salon', $deposit->retention_reason);
        $this->assertEquals(DepositStatus::PartiallyRefunded, $deposit->status);
    }

    public function test_refunding_with_retention_without_reason_fails(): void
    {
        $agency  = Agency::factory()->create();
        $deposit = Deposit::factory()->for($agency, 'agency')->held()->create();

        /** @var RefundDepositAction $action */
        $action = app(RefundDepositAction::class);

        $this->expectException(\InvalidArgumentException::class);
        $action->execute(
            deposit: $deposit,
            refundedAmount: 200000,
            retainedAmount: 100000,
            retentionReason: '', // Vide -> Doit lever une exception
            refundedAt: '2027-08-01'
        );
    }
}
