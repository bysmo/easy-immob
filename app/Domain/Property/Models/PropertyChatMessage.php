<?php

namespace App\Domain\Property\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'inquiry_id',
    'user_id',
    'message',
    'is_agency',
    'attachments',
])]
class PropertyChatMessage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_agency'   => 'boolean',
            'attachments' => 'array',
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(PropertyInquiry::class, 'inquiry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
