<?php

namespace App\Domain\Arrears\Models;

use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\ReminderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_id',
    'arrears_id',
    'channel',
    'sent_at',
    'content',
    'status',
])]
class Reminder extends Model
{
    /** @use HasFactory<ReminderFactory> */
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function arrear(): BelongsTo
    {
        return $this->belongsTo(Arrear::class, 'arrears_id');
    }
}
