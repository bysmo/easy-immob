<?php

namespace App\Domain\Owner\Models;

use App\Domain\Property\Models\Property;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'user_id',
    'reference',
    'first_name',
    'last_name',
    'company_name',
    'email',
    'phone',
    'address',
    'profession',
    'nationality',
    'id_card_number',
    'identity_document',
    'status',
])]
class Owner extends Model
{
    /** @use HasFactory<OwnerFactory> */
    use BelongsToAgency, HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'user_id',
        'reference',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'address',
        'profession',
        'nationality',
        'id_card_number',
        'identity_document',
        'status',
    ];

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

    /**
     * Le compte utilisateur du portail bailleur (si activé).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Indique si ce bailleur a un accès portail attribué.
     */
    public function hasPortalAccess(): bool
    {
        return $this->user_id !== null && $this->user?->hasRole('Bailleur');
    }

    /**
     * Indique si le portail du bailleur est actif (mot de passe créé, email vérifié).
     */
    public function isPortalActive(): bool
    {
        return $this->hasPortalAccess() && $this->user?->email_verified_at !== null;
    }

    public function managementContracts(): HasMany
    {
        return $this->hasMany(ManagementContract::class)->orderBy('created_at', 'desc');
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
