<?php

namespace App\Domain\Rent\Models;

use App\Domain\Lease\Models\Lease;
use App\Domain\Property\Models\Property;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_id',
    'property_id',
    'lease_id',
    'old_rent_amount',
    'new_rent_amount',
    'change_amount',
    'reason',
    'user_id',
    'effective_date',
])]
class RentHistory extends Model
{
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'old_rent_amount' => 'decimal:2',
            'new_rent_amount' => 'decimal:2',
            'change_amount'   => 'decimal:2',
            'effective_date'  => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
