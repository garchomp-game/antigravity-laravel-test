<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTenantScope
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTenantScope(): void
    {
        // Auto-filter by tenant_id on queries
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->id);
            }
        });

        // Auto-set tenant_id on creating
        static::creating(function (Model $model) {
            if (empty($model->tenant_id) && $tenant = Tenant::current()) {
                $model->tenant_id = $tenant->id;
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope to query without tenant filter.
     */
    public function scopeWithoutTenantScope(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('tenant');
    }
}
