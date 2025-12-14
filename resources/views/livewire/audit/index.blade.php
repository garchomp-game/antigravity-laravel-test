<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Audit Log</h2>

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            {{-- Action Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                <select
                    wire:model.live="actionFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    data-testid="action-filter"
                >
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ $action }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Actor Search --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Actor</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="actorSearch"
                    placeholder="Search by name or email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    data-testid="actor-search"
                >
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    data-testid="date-from"
                >
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    data-testid="date-to"
                >
            </div>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Summary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr data-testid="audit-row-{{ $log->id }}" class="hover:bg-gray-50 cursor-pointer" wire:click="showDetails('{{ $log->id }}')">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                @if(str_starts_with($log->action, 'ticket.')) bg-blue-100 text-blue-800
                                @elseif(str_starts_with($log->action, 'user.')) bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->actor?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->entity_type }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            @if(!empty($log->meta))
                                @php
                                    $summary = collect($log->meta)->map(fn($v, $k) => "$k: $v")->take(2)->implode(', ');
                                @endphp
                                {{ Str::limit($summary, 50) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button
                                wire:click.stop="showDetails('{{ $log->id }}')"
                                class="text-blue-600 hover:text-blue-800"
                                data-testid="view-details-{{ $log->id }}"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $this->selectedLog)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" data-testid="detail-modal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Audit Log Details</h3>
                    <button
                        wire:click="closeDetails"
                        class="text-gray-400 hover:text-gray-600"
                        data-testid="close-detail-modal"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Action</label>
                            <p class="text-gray-900">{{ $this->selectedLog->action }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Timestamp</label>
                            <p class="text-gray-900">{{ $this->selectedLog->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Actor</label>
                            <p class="text-gray-900">{{ $this->selectedLog->actor?->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Entity</label>
                            <p class="text-gray-900">{{ $this->selectedLog->entity_type }} ({{ $this->selectedLog->entity_id }})</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">IP Address</label>
                            <p class="text-gray-900">{{ $this->selectedLog->ip ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">User Agent</label>
                            <p class="text-gray-900 truncate">{{ $this->selectedLog->user_agent ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Request ID</label>
                            <p class="text-gray-900 font-mono text-xs">{{ $this->selectedLog->request_id }}</p>
                        </div>
                    </div>

                    @if(!empty($this->selectedLog->meta))
                        <div>
                            <label class="text-sm font-medium text-gray-500">Metadata</label>
                            <pre class="mt-1 p-3 bg-gray-100 rounded text-sm overflow-x-auto">{{ json_encode($this->selectedLog->meta, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        wire:click="closeDetails"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
