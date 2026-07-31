<?php

namespace App\Domain\Owner\Models;

use App\Domain\Property\Models\Property;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'reference',
    'first_name',
    'last_name',
    'company_name',
    'email',
    'phone',
    'address',
    'identity_document',
    'status',
])]
class Owner extends Model
{
    /** @use HasFactory<OwnerFactory> */
    use BelongsToAgency, HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // Accessors
    // ------------------------------------------------------------------

    /**
     * Nom complet : "Dupont Jean" ou raison sociale si personne morale.
     */
    public function getFullNameAttribute(): string
    {
        return $this->company_name
            ? $this->company_name
            : trim("{$this->last_name} {$this->first_name}");
    }

    // ------------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------------

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(OwnerPayout::class)->orderBy('created_at', 'desc');
    }

    public function payoutSettlements(): HasManyThrough
    {
        return $this->hasManyThrough(OwnerPayoutSettlement::class, OwnerPayout::class)->orderBy('payment_date', 'desc');
    }
}
