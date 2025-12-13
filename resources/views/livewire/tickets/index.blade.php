<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Tickets</h1>
        <a href="{{ route('tenant.tickets.create', ['tenant' => $tenantSlug]) }}" class="btn btn-primary">
            + New Ticket
        </a>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-control">
                    <input type="text" wire:model.live="search" placeholder="Search tickets..." class="input input-bordered w-full" />
                </div>
                <div class="form-control">
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <select wire:model.live="typeFilter" class="select select-bordered w-full">
                        <option value="">All Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Assignee</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="font-bold">{{ $ticket->title }}</div>
                                    <div class="text-sm opacity-50">{{ $ticket->team?->name }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-primary badge-sm">{{ $ticket->type_label }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($ticket->status) {
                                            'new' => 'badge-warning',
                                            'in_progress' => 'badge-info',
                                            'resolved' => 'badge-success',
                                            'closed' => 'badge-neutral',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} badge-sm">{{ $ticket->status_label }}</span>
                                </td>
                                <td>{{ $ticket->assignee?->name ?? '-' }}</td>
                                <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('tenant.tickets.show', ['tenant' => $tenantSlug, 'ticket' => $ticket->id]) }}" class="btn btn-ghost btn-xs">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/60">
                                    No tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</div>
