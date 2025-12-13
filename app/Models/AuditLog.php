<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory, HasTenantScope, HasUuid;

    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'request_id',
        'ip',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
