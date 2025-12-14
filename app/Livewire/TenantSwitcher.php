<?php

namespace App\Livewire;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TenantSwitcher extends Component
{
    public string $currentTenantSlug;
    public ?Tenant $currentTenant = null;

    public function mount(string $currentTenantSlug): void
    {
        $this->currentTenantSlug = $currentTenantSlug;
        $this->currentTenant = Tenant::where('slug', $currentTenantSlug)->first();
    }

    public function switchTo(string $slug): mixed
    {
        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            return null;
        }

        $user = Auth::user();
        if (!$user || !$user->canAccessTenant($tenant)) {
            return null;
        }

        return $this->redirect("/t/{$slug}/dashboard", navigate: false);
    }

    public function render()
    {
        $user = Auth::user();
        $tenants = $user ? $user->tenants()->get() : collect();

        // Fallback: if user has no entries in tenant_users, show their primary tenant
        if ($tenants->isEmpty() && $user && $user->tenant) {
            $tenants = collect([$user->tenant]);
        }

        return view('livewire.tenant-switcher', [
            'tenants' => $tenants,
            'currentTenant' => $this->currentTenant,
        ]);
    }
}
