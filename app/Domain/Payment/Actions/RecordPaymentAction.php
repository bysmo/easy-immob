<?php

namespace App\Domain\Payment\Actions;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Payment\Models\Payment;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordPaymentAction
{
    public function __construct(
        private ReferenceGenerator $referenceGenerator
    ) {}

    /**
     * Enregistre un paiement sur une échéance de loyer dans une transaction DB.
     */
    public function execute(
        RentSchedule $schedule,
        float $amount,
        string $paymentDate,
        string $paymentMethod = 'cash',
        ?string $notes = null,
        ?string $proofDocument = null
    ): Payment {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant du paiement doit être supérieur à zéro.");
        }

        return DB::transaction(function () use ($schedule, $amount, $paymentDate, $paymentMethod, $notes, $proofDocument) {
            $reference = $this->referenceGenerator->generate(Payment::class, $schedule->agency_id, 'PAY');

            $payment = Payment::create([
                'agency_id'        => $schedule->agency_id,
                'rent_schedule_id' => $schedule->id,
                'recorded_by_id'   => Auth::id(),
                'reference'        => $reference,
                'amount'           => $amount,
                'payment_date'     => $paymentDate,
                'payment_method'   => $paymentMethod,
                'proof_document'   => $proofDocument,
                'status'           => 'completed',
                'notes'            => $notes,
            ]);

            // Mettre à jour l'échéance de loyer
            $newPaidAmount = (float) $schedule->paid_amount + $amount;
            $expected      = (float) $schedule->expected_amount;
            $newRemaining  = max(0, $expected - $newPaidAmount);

            $newStatus = RentScheduleStatus::PartiallyPaid;
            if ($newRemaining == 0) {
                $newStatus = RentScheduleStatus::Paid;
            }

            $schedule->update([
                'paid_amount'      => $newPaidAmount,
                'remaining_amount' => $newRemaining,
                'status'           => $newStatus,
            ]);

            return $payment;
        });
    }
}
