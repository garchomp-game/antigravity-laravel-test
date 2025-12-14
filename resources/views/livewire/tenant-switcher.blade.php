<div data-testid="tenant-switcher" class="dropdown dropdown-end">
    <div 
        tabindex="0" 
        role="button" 
        data-testid="tenant-dropdown-trigger"
        class="btn btn-outline border-base-300 hover:border-primary hover:bg-base-200 gap-2 font-normal normal-case transition-all duration-300"
    >
        <span class="text-xl">🏢</span>
        <span class="font-medium hidden sm:inline">{{ $currentTenant?->name ?? 'Select Tenant' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <ul 
        tabindex="0" 
        data-testid="tenant-dropdown-menu"
        class="dropdown-content menu bg-base-100 rounded-box z-[100] w-64 p-2 shadow-xl border border-base-200 mt-2"
    >
        <li class="menu-title px-4 py-2 text-xs text-base-content/50 uppercase font-bold tracking-wider">Switch Tenant</li>
        @forelse($tenants as $tenant)
            <li>
                <button 
                    wire:click="switchTo('{{ $tenant->slug }}')"
                    data-testid="tenant-option-{{ $tenant->slug }}"
                    class="flex items-center justify-between py-3 {{ $currentTenant && $currentTenant->id === $tenant->id ? 'active font-bold' : '' }}"
                >
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="bg-neutral-focus text-neutral-content rounded w-8">
                                <span class="text-xs">{{ substr($tenant->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <span>{{ $tenant->name }}</span>
                    </div>
                    @if($currentTenant && $currentTenant->id === $tenant->id)
                        <span data-testid="current-tenant-indicator" class="badge badge-success badge-sm badge-outline">CURRENT</span>
                    @endif
                </button>
            </li>
        @empty
            <li class="disabled">
                <span class="text-gray-500 italic p-4 text-center">No tenants available</span>
            </li>
        @endforelse
    </ul>
</div>
