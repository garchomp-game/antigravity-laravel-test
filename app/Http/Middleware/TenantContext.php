<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantSlug = $request->route('tenant');

        if ($tenantSlug) {
            $tenant = Tenant::where('slug', $tenantSlug)->first();

            if (!$tenant) {
                abort(404, 'Tenant not found');
            }

            // Check if authenticated user belongs to this tenant
            $user = $request->user();
            if ($user) {
                // Multi-tenant: check tenant_users pivot table first, fallback to tenant_id
                $canAccess = $user->canAccessTenant($tenant) || $user->tenant_id === $tenant->id;
                if (!$canAccess) {
                    abort(404, 'Tenant not found');
                }
            }

            // Set the current tenant
            Tenant::setCurrent($tenant);

            // Store in request attributes for easy access
            $request->attributes->set('tenant', $tenant);
        }

        return $next($request);
    }
}
