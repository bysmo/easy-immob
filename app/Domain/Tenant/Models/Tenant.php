<?php

namespace App\Domain\Tenant\Models;

use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Models\Lease;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'user_id',
    'reference',
    'first_name',
    'last_name',
    'email',
    'phone',
    'address',
    'identity_document',
    'emergency_contact',
    'status',
])]
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use BelongsToAgency, HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // Accessors
    // ------------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name} {$this->first_name}");
    }

    public function getCodeLocataireAttribute(): string
    {
        return $this->reference;
    }

    // ------------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
