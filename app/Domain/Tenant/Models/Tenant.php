<?php

namespace App\Domain\Tenant\Models;

use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
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
}
