<?php

namespace App\Domain\Arrears\Models;

use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\ArrearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'agency_id',
    'lease_id',
    'rent_schedule_id',
    'tenant_id',
    'amount_due',
    'amount_paid',
    'remaining_amount',
    'first_overdue_date',
    'severity',
    'status',
])]
class Arrear extends Model
{
    /** @use HasFactory<ArrearFactory> */
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'amount_due'         => 'decimal:2',
            'amount_paid'        => 'decimal:2',
            'remaining_amount'   => 'decimal:2',
            'first_overdue_date' => 'date',
            'severity'           => ArrearSeverity::class,
            'status'             => ArrearStatus::class,
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function rentSchedule(): BelongsTo
    {
        return $this->belongsTo(RentSchedule::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'arrears_id');
    }
}
