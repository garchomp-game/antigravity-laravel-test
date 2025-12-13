<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEvent extends Model
{
    use HasFactory, HasTenantScope, HasUuid;

    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'kind',
        'body',
        'from_status',
        'to_status',
        'actor_id',
    ];

    const KIND_COMMENT = 'comment';
    const KIND_STATUS_CHANGE = 'status_change';
    const KIND_ASSIGNMENT = 'assignment';
    const KIND_SYSTEM = 'system';

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
