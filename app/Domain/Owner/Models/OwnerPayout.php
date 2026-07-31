<?php

namespace App\Domain\Owner\Models;

use App\Domain\Owner\Enums\OwnerPayoutCalculationType;
use App\Domain\Owner\Enums\OwnerPayoutStatus;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'owner_id',
    'reference',
    'period',
    'period_start',
    'period_end',
    'calculation_type',
    'gross_amount',
    'commission_rate',
    'commission_amount',
    'irf_amount',
    'other_deductions_amount',
    'net_amount',
    'paid_amount',
    'status',
    'notes',
    'created_by',
])]
class OwnerPayout extends Model
{
    use BelongsToAgency, HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'period_start'            => 'date',
            'period_end'              => 'date',
            'calculation_type'        => OwnerPayoutCalculationType::class,
            'gross_amount'            => 'decimal:2',
            'commission_rate'         => 'float',
            'commission_amount'       => 'decimal:2',
            'irf_amount'              => 'decimal:2',
            'other_deductions_amount' => 'decimal:2',
            'net_amount'              => 'decimal:2',
            'paid_amount'             => 'decimal:2',
            'status'                  => OwnerPayoutStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OwnerPayoutItem::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(OwnerPayoutSettlement::class)->orderBy('payment_date', 'desc');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, round((float) $this->net_amount - (float) $this->paid_amount, 2));
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }
}
