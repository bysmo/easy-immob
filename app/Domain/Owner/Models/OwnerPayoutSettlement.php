<?php

namespace App\Domain\Owner\Models;

use App\Domain\Payment\Enums\PaymentMethod;
use App\Models\User;
use App\Support\Tenancy\BelongsToAgency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'agency_id',
    'owner_payout_id',
    'reference',
    'payment_date',
    'amount',
    'payment_method',
    'proof_document_path',
    'transaction_reference',
    'notes',
    'created_by',
])]
class OwnerPayoutSettlement extends Model
{
    use BelongsToAgency, HasFactory;

    protected function casts(): array
    {
        return [
            'payment_date'   => 'date',
            'amount'         => 'decimal:2',
            'payment_method' => PaymentMethod::class,
        ];
    }

    public function ownerPayout(): BelongsTo
    {
        return $this->belongsTo(OwnerPayout::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getProofDocumentUrlAttribute(): ?string
    {
        if (! $this->proof_document_path) {
            return null;
        }

        return Storage::url($this->proof_document_path);
    }
}
