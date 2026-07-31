<?php

namespace App\Domain\Owner\Models;

use App\Domain\Property\Models\Property;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'owner_payout_id',
    'property_id',
    'rent_schedule_id',
    'gross_amount',
    'commission_amount',
    'irf_amount',
    'net_amount',
    'description',
])]
class OwnerPayoutItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'gross_amount'      => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'irf_amount'        => 'decimal:2',
            'net_amount'        => 'decimal:2',
        ];
    }

    public function ownerPayout(): BelongsTo
    {
        return $this->belongsTo(OwnerPayout::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rentSchedule(): BelongsTo
    {
        return $this->belongsTo(RentSchedule::class);
    }
}
