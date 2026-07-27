<?php

namespace App\Domain\Lease\Models;

use App\Domain\Deposit\Models\Deposit;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\LeaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'agency_id',
    'reference',
    'property_id',
    'tenant_id',
    'template_id',
    'start_date',
    'end_date',
    'rent_amount',
    'charges_amount',
    'payment_due_day',
    'deposit_amount',
    'status',
    'signed_at',
    'terminated_at',
])]
class Lease extends Model
{
    /** @use HasFactory<LeaseFactory> */
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'start_date'     => 'date',
            'end_date'       => 'date',
            'rent_amount'    => 'decimal:2',
            'charges_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'status'         => LeaseStatus::class,
            'signed_at'      => 'datetime',
            'terminated_at'  => 'datetime',
        ];
    }

    public function getTotalMonthlyAmountAttribute(): float
    {
        return (float) $this->rent_amount + (float) $this->charges_amount;
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(LeaseTemplate::class, 'template_id');
    }

    public function rentSchedules(): HasMany
    {
        return $this->hasMany(RentSchedule::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
