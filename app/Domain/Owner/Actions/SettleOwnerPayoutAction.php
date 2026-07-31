<?php

namespace App\Domain\Owner\Actions;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Owner\Models\OwnerPayoutSettlement;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettleOwnerPayoutAction
{
    /**
     * Enregistre un règlement sur une facture de reversement bailleur.
     *
     * @param OwnerPayout $payout
     * @param array{
     *     payment_date: string,
     *     amount: float,
     *     payment_method: string,
     *     transaction_reference?: string|null,
     *     proof_document?: UploadedFile|string|null,
     *     notes?: string|null
     * } $data
     * @param User|null $user
     * @return OwnerPayoutSettlement
     */
    public function execute(OwnerPayout $payout, array $data, ?User $user = null): OwnerPayoutSettlement
    {
        return DB::transaction(function () use ($payout, $data, $user) {
            $proofPath = null;
            if (isset($data['proof_document']) && $data['proof_document'] instanceof UploadedFile) {
                $proofPath = $data['proof_document']->store('payout_proofs', 'public');
            } elseif (isset($data['proof_document']) && is_string($data['proof_document'])) {
                $proofPath = $data['proof_document'];
            }

            $settlementRef = $this->generateSettlementReference($payout->agency_id);
            $amount = (float) $data['amount'];

            $settlement = OwnerPayoutSettlement::create([
                'agency_id'             => $payout->agency_id,
                'owner_payout_id'       => $payout->id,
                'reference'             => $settlementRef,
                'payment_date'          => $data['payment_date'],
                'amount'                => $amount,
                'payment_method'        => $data['payment_method'],
                'proof_document_path'   => $proofPath,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'notes'                 => $data['notes'] ?? null,
                'created_by'            => $user?->id,
            ]);

            // Mise à jour du solde de la facture
            $oldPaid = (float) $payout->paid_amount;
            $newPaid = round($oldPaid + $amount, 2);
            $payout->paid_amount = $newPaid;

            $net = (float) $payout->net_amount;
            if ($newPaid >= $net) {
                $payout->status = 'paid';
            } elseif ($newPaid > 0) {
                $payout->status = 'partially_paid';
            }

            $payout->save();

            AuditLogger::log('payout.settled', $payout, [
                'paid_amount' => $oldPaid,
                'status'      => $payout->getOriginal('status'),
            ], [
                'paid_amount'       => $newPaid,
                'status'            => $payout->status,
                'settlement_amount' => $amount,
                'payment_method'    => $data['payment_method'],
            ]);

            return $settlement;
        });
    }

    /**
     * Génère une référence séquentielle unique pour le règlement : REG-2026-0001
     */
    private function generateSettlementReference(int $agencyId): string
    {
        $year = date('Y');
        $prefix = "REG-{$year}-";

        $lastSettlement = OwnerPayoutSettlement::where('agency_id', $agencyId)
            ->where('reference', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastSettlement) {
            return "{$prefix}0001";
        }

        $lastSeq = (int) substr($lastSettlement->reference, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextSeq}";
    }
}
