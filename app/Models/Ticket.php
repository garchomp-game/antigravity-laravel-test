<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory, HasTenantScope, HasUuid;

    protected $fillable = [
        'tenant_id',
        'type',
        'status',
        'title',
        'description',
        'priority',
        'created_by',
        'assigned_to',
        'department_id',
        'team_id',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return config('opshub.ticket_types.' . $this->type, $this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return config('opshub.ticket_statuses.' . $this->status, $this->status);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = config('opshub.status_transitions.' . $this->status, []);
        return in_array($newStatus, $allowed);
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }

    public function getPriorityLabelAttribute(): string
    {
        return config('opshub.priorities.' . $this->priority, 'Unknown');
    }
}
