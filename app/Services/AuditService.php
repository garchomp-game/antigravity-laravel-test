<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * Record an audit log entry.
     */
    public function record(
        string $action,
        string $entityType,
        string $entityId,
        array $meta = [],
        ?string $actorId = null
    ): AuditLog {
        $request = request();

        return AuditLog::create([
            'tenant_id' => Tenant::current()?->id,
            'actor_id' => $actorId ?? auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'request_id' => $this->getRequestId($request),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'meta' => $meta,
        ]);
    }

    /**
     * Get or generate a request ID.
     */
    protected function getRequestId(Request $request): string
    {
        return $request->header('X-Request-Id') 
            ?? $request->attributes->get('request_id') 
            ?? (string) Str::uuid();
    }
}
