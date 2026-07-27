<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Domain\Payment\Enums\PaymentMethod;
use App\Domain\Payment\Models\Payment;
use App\Domain\Rent\Models\RentSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $agency = Agency::factory()->create();

        return [
            'agency_id'        => $agency->id,
            'rent_schedule_id' => RentSchedule::factory()->for($agency, 'agency'),
            'recorded_by_id'   => User::factory()->for($agency, 'agency'),
            'reference'        => 'PAY-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'amount'           => 160000,
            'payment_date'     => now()->format('Y-m-d'),
            'payment_method'   => PaymentMethod::Cash,
            'proof_document'   => null,
            'status'           => 'completed',
            'notes'            => null,
        ];
    }
}
