<?php

namespace App\Domain\Property\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'agency_id',
    'property_id',
    'tenant_id',
    'user_id',
    'subject',
    'status',
])]
class PropertyInquiry extends Model
{
    use BelongsToAgency, HasFactory;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PropertyChatMessage::class, 'inquiry_id')->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(PropertyChatMessage::class, 'inquiry_id')->latestOfMany();
    }
}
