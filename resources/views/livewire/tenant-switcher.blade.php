<div data-testid="tenant-switcher" class="dropdown dropdown-end">
    <div 
        tabindex="0" 
        role="button" 
        data-testid="tenant-dropdown-trigger"
        class="btn btn-ghost gap-2"
    >
        <span class="hidden sm:inline">🏢</span>
        <span>{{ $currentTenant?->name ?? 'Select Tenant' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <ul 
        tabindex="0" 
        data-testid="tenant-dropdown-menu"
        class="dropdown-content menu bg-base-100 rounded-box z-[100] w-56 p-2 shadow-lg border border-base-300"
    >
        @forelse($tenants as $tenant)
            <li>
                <button 
                    wire:click="switchTo('{{ $tenant->slug }}')"
                    data-testid="tenant-option-{{ $tenant->slug }}"
                    class="flex items-center justify-between {{ $currentTenant && $currentTenant->id === $tenant->id ? 'active' : '' }}"
                >
                    <span>{{ $tenant->name }}</span>
                    @if($currentTenant && $currentTenant->id === $tenant->id)
                        <span data-testid="current-tenant-indicator" class="text-success">✓</span>
                    @endif
                </button>
            </li>
        @empty
            <li class="disabled">
                <span class="text-gray-500">No tenants available</span>
            </li>
        @endforelse
    </ul>
</div>
