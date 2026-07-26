<?php

namespace App\Support\Tenancy;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToAgency
{
    public static function bootBelongsToAgency(): void
    {
        static::addGlobalScope(new AgencyScope);

        static::creating(function ($model) {
            if (Auth::hasUser()) {
                $model->agency_id = Auth::user()->agency_id;
            }
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
