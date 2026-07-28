<?php

namespace App\Domain\Subscription\Models;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'description',
    'max_properties',
    'price_monthly',
    'price_yearly',
    'features',
    'is_active',
    'is_popular',
])]
class SubscriptionPlan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'max_properties' => 'integer',
            'price_monthly' => 'decimal:2',
            'price_yearly'  => 'decimal:2',
            'features'       => 'array',
            'is_active'      => 'boolean',
            'is_popular'     => 'boolean',
        ];
    }

    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    public function getPriceForCycle(string $cycle): float
    {
        return $cycle === 'yearly' ? (float) $this->price_yearly : (float) $this->price_monthly;
    }

    public function getFormattedPriceForCycle(string $cycle): string
    {
        $price = $this->getPriceForCycle($cycle);
        return number_format($price, 0, ',', ' ') . ' FCFA';
    }

    public function isUnlimitedProperties(): bool
    {
        return $this->max_properties >= 99999;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
