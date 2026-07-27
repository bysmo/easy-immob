<?php

namespace App\Domain\Rent\Models;

use App\Domain\Lease\Models\Lease;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\RentScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_id',
    'lease_id',
    'period',
    'due_date',
    'expected_amount',
    'paid_amount',
    'remaining_amount',
    'status',
])]
class RentSchedule extends Model
{
    /** @use HasFactory<RentScheduleFactory> */
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'due_date'         => 'date',
            'expected_amount'  => 'decimal:2',
            'paid_amount'      => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'status'           => RentScheduleStatus::class,
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
