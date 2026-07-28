<?php

namespace App\Domain\Subscription\Models;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'number',
    'agency_id',
    'subscription_plan_id',
    'billing_cycle',
    'amount',
    'tax_amount',
    'total_amount',
    'status',
    'invoice_date',
    'due_date',
    'paid_at',
    'payment_method',
    'notes',
])]
class SaasInvoice extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
            'invoice_date' => 'date',
            'due_date'     => 'date',
            'paid_at'      => 'datetime',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'Payée',
            'unpaid'    => 'En attente',
            'overdue'   => 'En retard',
            'cancelled' => 'Annulée',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
            'unpaid'    => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
            'overdue'   => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300',
            'cancelled' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
            default     => 'bg-slate-100 text-slate-800',
        };
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $next = $last + 1;

        return sprintf('INV-SAAS-%s-%04d', $year, $next);
    }
}
