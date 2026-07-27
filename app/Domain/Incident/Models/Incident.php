<?php

namespace App\Domain\Incident\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Incident\Enums\IncidentStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Property\Models\Property;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'property_id',
    'lease_id',
    'tenant_id',
    'reference',
    'title',
    'description',
    'audio_path',
    'photos',
    'videos',
    'status',
    'priority',
    'repair_details',
    'repair_cost',
    'tenant_confirmation_photo',
    'tenant_confirmation_note',
    'resolved_at',
    'closed_at',
])]
class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'photos'      => 'array',
            'videos'      => 'array',
            'status'      => IncidentStatus::class,
            'repair_cost' => 'decimal:2',
            'resolved_at' => 'datetime',
            'closed_at'   => 'datetime',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
