<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit($model, 'created', [], $model->toArray());
        });

        static::updated(function ($model) {
            $changed = $model->getDirty();
            $old = [];
            $new = [];
            foreach ($changed as $key => $value) {
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $value;
            }
            if (! empty($new)) {
                static::logAudit($model, 'updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            static::logAudit($model, 'deleted', $model->toArray(), []);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                static::logAudit($model, 'restored', [], $model->toArray());
            });
        }
    }

    protected static function logAudit($model, string $event, array $old, array $new): void
    {
        $user = Auth::user();

        AuditLog::create([
            'user_id' => $user?->id,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->id,
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
