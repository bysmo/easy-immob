<?php

namespace App\Domain\Notification\Models;

use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\SystemNotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'agency_id',
    'recipient_type',
    'recipient_id',
    'type',
    'channel',
    'subject',
    'content',
    'sent_at',
    'status',
])]
class SystemNotification extends Model
{
    /** @use HasFactory<SystemNotificationFactory> */
    use BelongsToAgency, HasFactory;

    protected $table = 'notifications';

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }
}
