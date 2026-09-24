<?php

namespace App\Services;

use App\Models\FeedbackAuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function log(
        string $action,
        ?Model $entity = null,
        array $old = [],
        array $new = [],
    ): void {
        FeedbackAuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entity ? class_basename($entity) : null,
            'entity_id' => $entity?->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
            'created_at' => now(),
        ]);
    }
}
