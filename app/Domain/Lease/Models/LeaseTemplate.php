<?php

namespace App\Domain\Lease\Models;

use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\LeaseTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['agency_id', 'name', 'description', 'content', 'version', 'status'])]
class LeaseTemplate extends Model
{
    /** @use HasFactory<LeaseTemplateFactory> */
    use BelongsToAgency, HasFactory;

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'template_id');
    }
}
