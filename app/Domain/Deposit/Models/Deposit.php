<?php

namespace App\Domain\Deposit\Models;

use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Lease\Models\Lease;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\DepositFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_id',
    'lease_id',
    'expected_amount',
    'received_amount',
    'received_at',
    'retained_amount',
    'retention_reason',
    'refunded_amount',
    'refunded_at',
    'status',
])]
class Deposit extends Model
{
    /** @use HasFactory<DepositFactory> */
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'retained_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'received_at'      => 'date',
            'refunded_at'      => 'date',
            'status'           => DepositStatus::class,
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
