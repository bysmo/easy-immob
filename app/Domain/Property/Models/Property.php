<?php

namespace App\Domain\Property\Models;

use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_id',
    'reference',
    'owner_id',
    'property_type_id',
    'title',
    'description',
    'address',
    'city',
    'neighborhood',
    'surface_area',
    'bedrooms',
    'bathrooms',
    'rent_amount',
    'status',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use BelongsToAgency, HasFactory;

    // ------------------------------------------------------------------
    // Casts
    // ------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'surface_area' => 'decimal:2',
            'rent_amount'  => 'decimal:2',
            'status'       => PropertyStatus::class,
        ];
    }

    // ------------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }
}
