<?php

namespace Tests\Feature\Domain\Arrears;

use App\Domain\Agency\Models\Agency;
use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Services\ArrearDetector;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArrearDetectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_detector_creates_arrear_folder_for_overdue_schedule(): void
    {
        $agency   = Agency::factory()->create();
        $schedule = RentSchedule::factory()->for($agency, 'agency')->create([
            'due_date'         => now()->subDays(5)->format('Y-m-d'),
            'expected_amount'  => 150000,
            'paid_amount'      => 0,
            'remaining_amount' => 150000,
            'status'           => RentScheduleStatus::Pending,
        ]);

        /** @var ArrearDetector $detector */
        $detector = app(ArrearDetector::class);
        $count    = $detector->detect();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('arrears', [
            'agency_id'        => $agency->id,
            'rent_schedule_id' => $schedule->id,
            'remaining_amount' => 150000,
            'severity'         => ArrearSeverity::Warning->value,
            'status'           => ArrearStatus::Open->value,
        ]);

        $this->assertEquals(RentScheduleStatus::Overdue, $schedule->fresh()->status);
    }

    public function test_detector_calculates_critical_severity_for_late_overdue(): void
    {
        $agency   = Agency::factory()->create();
        $schedule = RentSchedule::factory()->for($agency, 'agency')->create([
            'due_date'         => now()->subDays(20)->format('Y-m-d'),
            'remaining_amount' => 200000,
        ]);

        /** @var ArrearDetector $detector */
        $detector = app(ArrearDetector::class);
        $detector->detect();

        $arrear = Arrear::withoutGlobalScopes()->where('rent_schedule_id', $schedule->id)->first();
        $this->assertNotNull($arrear);
        $this->assertEquals(ArrearSeverity::Critical, $arrear->severity);
    }

    public function test_detector_marks_arrear_as_settled_when_remaining_amount_reaches_zero(): void
    {
        $agency = Agency::factory()->create();
        $arrear = Arrear::factory()->for($agency, 'agency')->create([
            'remaining_amount' => 100000,
            'status'           => ArrearStatus::Open,
        ]);

        // Simuler le paiement complet de l'échéance
        $arrear->rentSchedule->update([
            'remaining_amount' => 0,
            'paid_amount'      => 100000,
            'status'           => RentScheduleStatus::Paid,
        ]);

        /** @var ArrearDetector $detector */
        $detector = app(ArrearDetector::class);
        $detector->detect();

        $this->assertEquals(ArrearStatus::Settled, $arrear->fresh()->status);
    }
}
