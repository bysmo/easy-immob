<?php

namespace App\Domain\Audit\Services;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return AuditLog::create([
            'agency_id'   => $user?->agency_id ?? $model->getAttribute('agency_id'),
            'user_id'     => $user?->id,
            'action'      => $action,
            'entity_type' => get_class($model),
            'entity_id'   => $model->getKey(),
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
