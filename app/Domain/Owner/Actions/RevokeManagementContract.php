<?php

namespace App\Domain\Owner\Actions;

use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Résiliation immédiate d'un mandat de gestion par le bailleur.
 * Le statut passe à « Terminated » et une notification est envoyée
 * aux agents de l'agence.
 */
class RevokeManagementContract
{
    public function execute(ManagementContract $contract, User $owner): void
    {
        if ($contract->status !== ManagementContractStatus::Active) {
            throw new \RuntimeException("Ce mandat ne peut pas être résilié (statut : {$contract->status->label()}).");
        }

        DB::transaction(function () use ($contract, $owner) {
            $contract->update([
                'status'    => ManagementContractStatus::Terminated,
                'signed_at' => $contract->signed_at ?? now(),
            ]);

            // Notifier les agents de l'agence
            $agencyUsers = \App\Models\User::withoutGlobalScopes()
                ->where('agency_id', $contract->agency_id)
                ->get();

            foreach ($agencyUsers as $agencyUser) {
                $agencyUser->notify(new \App\Notifications\ManagementContractRevokedNotification(
                    $contract,
                    $owner,
                ));
            }
        });
    }
}
