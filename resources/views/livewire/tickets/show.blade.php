<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('tenant.tickets.index', ['tenant' => $tenantSlug]) }}" class="btn btn-ghost btn-sm mb-2">
                ← Back to Tickets
            </a>
            <h1 class="text-3xl font-bold">{{ $ticket->title }}</h1>
        </div>
        <div class="flex gap-2">
            @php
                $badgeClass = match($ticket->status) {
                    'new' => 'badge-warning',
                    'in_progress' => 'badge-info',
                    'resolved' => 'badge-success',
                    'closed' => 'badge-neutral',
                    default => 'badge-ghost',
                };
            @endphp
            <span class="badge {{ $badgeClass }} badge-lg">{{ $ticket->status_label }}</span>
            <span class="badge badge-primary badge-lg">{{ $ticket->type_label }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Description</h2>
                    <p class="whitespace-pre-wrap">{{ $ticket->description ?? 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Activity / Events -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Activity</h2>

                    <!-- Add Comment Form -->
                    <form wire:submit="addComment" class="mb-4">
                        <div class="form-control">
                            <textarea 
                                wire:model="newComment" 
                                class="textarea textarea-bordered w-full" 
                                placeholder="Add a comment..."
                                rows="3"
                            ></textarea>
                            @error('newComment') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
                        </div>
                    </form>

                    <!-- Events List -->
                    <div class="divider"></div>
                    <div class="space-y-4">
                        @forelse($events as $event)
                            <div class="flex gap-3">
                                <div class="avatar placeholder">
                                    <div class="bg-neutral-focus text-neutral-content rounded-full w-8">
                                        <span class="text-xs">{{ substr($event->actor?->name ?? '?', 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-semibold">{{ $event->actor?->name ?? 'System' }}</span>
                                            @if($event->kind === 'comment')
                                                <span class="text-base-content/60">commented</span>
                                            @elseif($event->kind === 'status_change')
                                                <span class="text-base-content/60">
                                                    changed status from 
                                                    <span class="badge badge-xs">{{ $event->from_status }}</span>
                                                    to
                                                    <span class="badge badge-xs badge-primary">{{ $event->to_status }}</span>
                                                </span>
                                            @elseif($event->kind === 'assignment')
                                                <span class="text-base-content/60">{{ $event->body }}</span>
                                            @else
                                                <span class="text-base-content/60">{{ $event->body }}</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-base-content/50">{{ $event->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($event->kind === 'comment' && $event->body)
                                        <p class="mt-1 text-sm bg-base-200 rounded p-2">{{ $event->body }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-base-content/60">No activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Details</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Priority</span>
                            <span class="font-semibold">{{ $ticket->priority_label }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Team</span>
                            <span>{{ $ticket->team?->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Created by</span>
                            <span>{{ $ticket->creator?->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Created</span>
                            <span>{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Card -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Assignee</h2>
                    <div class="form-control">
                        <select wire:model="selectedAssignee" class="select select-bordered select-sm w-full">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="assignTicket" class="btn btn-sm btn-outline mt-2">Update Assignee</button>
                </div>
            </div>

            <!-- Status Transition Card -->
            @if(count($allowedTransitions) > 0)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Change Status</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($allowedTransitions as $status)
                            <button 
                                wire:click="transitionStatus('{{ $status }}')"
                                class="btn btn-sm btn-outline"
                            >
                                {{ $statuses[$status] ?? $status }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
