<?php

namespace Tests\Feature\Domain\Report;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;

use App\Domain\Lease\Models\Lease;
use App\Domain\Report\Services\FinancialReportService;
use App\Domain\Report\Services\OwnerStatementService;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_financial_report_service_calculates_summary(): void
    {
        $agency = Agency::factory()->create();
        $user   = \App\Models\User::factory()->for($agency, 'agency')->create();
        $lease  = Lease::factory()->for($agency, 'agency')->create();
        $this->actingAs($user);

        RentSchedule::factory()->create([
            'agency_id'        => $agency->id,
            'lease_id'         => $lease->id,
            'period'           => '2026-01',
            'expected_amount'  => 200000,
            'paid_amount'      => 200000,
            'remaining_amount' => 0,
            'status'           => RentScheduleStatus::Paid,
            'due_date'         => now()->day(15)->format('Y-m-d'),
        ]);

        RentSchedule::factory()->create([
            'agency_id'        => $agency->id,
            'lease_id'         => $lease->id,
            'period'           => '2026-02',
            'expected_amount'  => 100000,
            'paid_amount'      => 0,
            'remaining_amount' => 100000,
            'status'           => RentScheduleStatus::Overdue,
            'due_date'         => now()->day(15)->format('Y-m-d'),
        ]);

        /** @var FinancialReportService $service */
        $service = app(FinancialReportService::class);
        $summary = $service->getSummary(now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d'));

        $this->assertEquals(300000, $summary['expected_total']);
        $this->assertEquals(200000, $summary['collected_total']);
        $this->assertEquals(100000, $summary['remaining_total']);
        $this->assertEquals(66.67, $summary['collection_rate']);
    }

    public function test_owner_statement_service_calculates_fees_and_net_payable(): void
    {
        $agency   = Agency::factory()->create();
        $owner    = Owner::factory()->for($agency, 'agency')->create();
        $property = Property::factory()->for($agency, 'agency')->create(['owner_id' => $owner->id]);

        RentSchedule::factory()->for($agency, 'agency')->create([
            'expected_amount'  => 500000,
            'paid_amount'      => 500000,
            'remaining_amount' => 0,
            'status'           => RentScheduleStatus::Paid,
        ]);

        // Associer ce schedule au bien du propriétaire
        $schedule = RentSchedule::first();
        $schedule->lease->update(['property_id' => $property->id]);

        /** @var OwnerStatementService $service */
        $service   = app(OwnerStatementService::class);
        $statement = $service->generateStatement($owner, 10.0);

        $this->assertEquals(500000, $statement['total_collected']);
        $this->assertEquals(50000, $statement['management_fee_amount']); // 10% de 500.000
        $this->assertEquals(450000, $statement['net_payable']);
    }
}
