<?php

namespace App\Notifications;

use App\Domain\Owner\Models\ManagementContract;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManagementContractRevokedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ManagementContract $contract,
        public User $owner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Le bailleur {$this->owner->owner?->full_name} a résilié le mandat #{$this->contract->reference}.",
            'type'    => 'contract_revoked',
            'contract_id' => $this->contract->id,
        ];
    }
}
