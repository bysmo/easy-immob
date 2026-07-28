<?php

namespace App\Domain\Property\Models;

use App\Domain\Incident\Models\Incident;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    'latitude',
    'longitude',
    'google_maps_url',
    'surface_area',
    'bedrooms',
    'bathrooms',
    'rent_amount',
    'photos',
    'videos',
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
            'latitude'     => 'float',
            'longitude'    => 'float',
            'photos'       => 'array',
            'videos'       => 'array',
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

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function rentHistories(): HasMany
    {
        return $this->hasMany(\App\Domain\Rent\Models\RentHistory::class)->orderBy('created_at', 'desc');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class)->orderBy('updated_at', 'desc');
    }

    /**
     * Calcule le coût réel d'entretien du bien (somme des coûts de réparation des incidents résolus ou clôturés).
     */
    public function getTotalMaintenanceCostAttribute(): float
    {
        return (float) $this->incidents()
            ->whereIn('status', ['resolved', 'closed'])
            ->sum('repair_cost');
    }

    /**
     * Retourne la liste des photos ou des images de remplacement par défaut (maximum 10 photos).
     */
    public function getPhotoListAttribute(): array
    {
        $photos = $this->photos && is_array($this->photos) && count($this->photos) > 0
            ? $this->photos
            : ['https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1000&q=80'];
        
        return array_slice($photos, 0, 10);
    }

    /**
     * Retourne la liste des vidéos (maximum 3 vidéos).
     */
    public function getVideoListAttribute(): array
    {
        $vids = $this->videos && is_array($this->videos) ? $this->videos : [];
        return array_slice($vids, 0, 3);
    }
}
