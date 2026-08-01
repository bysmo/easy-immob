<?php

namespace App\Domain\Owner\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Property\Models\Property;
use App\Support\Tenancy\BelongsToAgency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'owner_id',
    'reference',
    'title',
    'start_date',
    'end_date',
    'duration_months',
    'commission_type',
    'commission_value',
    'agreed_rent_amount',
    'irf_paid_by_owner',
    'caution_kept_by_agency',
    'notice_period_months',
    'payment_bank_details',
    'terms_and_conditions',
    'status',
    'signed_at',
    'document_path',
])]
class ManagementContract extends Model
{
    use BelongsToAgency, HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date'             => 'date',
            'end_date'               => 'date',
            'duration_months'        => 'integer',
            'commission_value'       => 'decimal:2',
            'agreed_rent_amount'     => 'decimal:2',
            'irf_paid_by_owner'      => 'boolean',
            'caution_kept_by_agency' => 'boolean',
            'notice_period_months'   => 'integer',
            'status'                 => ManagementContractStatus::class,
            'signed_at'              => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'management_contract_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function getFormattedCommissionAttribute(): string
    {
        if ($this->commission_type === 'fixed') {
            return number_format((float) $this->commission_value, 0, ',', ' ') . ' FCFA / mois';
        }

        return number_format((float) $this->commission_value, 1, ',', ' ') . ' %';
    }
}
