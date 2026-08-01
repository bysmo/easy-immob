<?php

namespace App\Domain\Agency\Models;

use App\Domain\Property\Models\Property;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'legal_name',
    'manager_name',
    'manager_title',
    'manager_phone',
    'manager_id_card',
    'email',
    'phone',
    'address',
    'logo_path',
    'commission_rate',
    'is_subject_to_tva',
    'tva_rate',
    'nif_rccm',
    'status',
    'subscription_plan_id',
    'billing_cycle',
    'subscription_status',
    'subscription_ends_at',
    'trial_ends_at',
])]
class Agency extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'commission_rate'      => 'float',
            'is_subject_to_tva'    => 'boolean',
            'tva_rate'             => 'float',
            'subscription_ends_at' => 'datetime',
            'trial_ends_at'        => 'datetime',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::disk('public')->url($this->logo_path);
        }

        return null;
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function saasInvoices(): HasMany
    {
        return $this->hasMany(SaasInvoice::class)->orderBy('created_at', 'desc');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->withoutGlobalScopes()->count();
    }

    public function getMaxPropertiesLimitAttribute(): int
    {
        if (!$this->subscriptionPlan) {
            return 10; // Quota par défaut si non renseigné
        }

        return $this->subscriptionPlan->max_properties;
    }

    public function hasReachedPropertyLimit(): bool
    {
        if ($this->subscriptionPlan && $this->subscriptionPlan->isUnlimitedProperties()) {
            return false;
        }

        return $this->properties_count >= $this->max_properties_limit;
    }

    public function getPropertiesUsagePercentageAttribute(): float
    {
        $limit = $this->max_properties_limit;
        if ($limit >= 99999) {
            return 0;
        }

        return min(100, round(($this->properties_count / max(1, $limit)) * 100, 1));
    }

    public function getRemainingPropertiesCountAttribute(): int
    {
        $limit = $this->max_properties_limit;
        if ($limit >= 99999) {
            return 999999;
        }

        return max(0, $limit - $this->properties_count);
    }
}
