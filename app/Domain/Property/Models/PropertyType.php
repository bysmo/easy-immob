<?php

namespace App\Domain\Property\Models;

use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\PropertyTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['agency_id', 'name', 'description', 'status'])]
class PropertyType extends Model
{
    /** @use HasFactory<PropertyTypeFactory> */
    use BelongsToAgency, HasFactory;

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
