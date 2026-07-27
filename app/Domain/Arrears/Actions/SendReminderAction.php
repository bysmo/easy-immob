<?php

namespace App\Domain\Arrears\Actions;

use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Models\Reminder;
use App\Domain\Notification\Models\SystemNotification;

class SendReminderAction
{
    public function execute(Arrear $arrear, string $channel = 'email', ?string $customMessage = null): Reminder
    {
        $arrear->loadMissing(['tenant', 'rentSchedule', 'lease.property']);

        $defaultMessage = sprintf(
            "Rappel de loyer impayé : Bonjour %s, votre loyer pour la période %s d'un montant restant de %s FCFA sur le bien %s est en retard. Merci de procéder au règlement.",
            $arrear->tenant?->full_name,
            $arrear->rentSchedule?->period,
            number_format((float) $arrear->remaining_amount, 0, ',', ' '),
            $arrear->lease?->property?->title
        );

        $content = $customMessage ?: $defaultMessage;

        // Enregistrer la relance
        $reminder = Reminder::create([
            'agency_id'  => $arrear->agency_id,
            'arrears_id' => $arrear->id,
            'channel'    => $channel,
            'sent_at'    => now(),
            'content'    => $content,
            'status'     => 'sent',
        ]);

        // Enregistrer la notification dans le système
        if ($arrear->tenant) {
            SystemNotification::create([
                'agency_id'      => $arrear->agency_id,
                'recipient_type' => get_class($arrear->tenant),
                'recipient_id'   => $arrear->tenant->id,
                'type'           => 'arrear_reminder',
                'channel'        => $channel,
                'subject'        => 'Relance de loyer impayé - ' . $arrear->rentSchedule?->period,
                'content'        => $content,
                'sent_at'        => now(),
                'status'         => 'sent',
            ]);
        }

        return $reminder;
    }
}
