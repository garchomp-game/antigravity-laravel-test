<div>
    <h1 class="text-3xl font-bold mb-6">Create Ticket</h1>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Type -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Type *</span>
                        </label>
                        <select wire:model="type" class="select select-bordered w-full">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Priority -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Priority</span>
                        </label>
                        <select wire:model="priority" class="select select-bordered w-full">
                            @foreach($priorities as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Title -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Title *</span>
                    </label>
                    <input type="text" wire:model="title" placeholder="Brief description of the issue" class="input input-bordered w-full" />
                    @error('title') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea wire:model="description" rows="4" placeholder="Detailed description..." class="textarea textarea-bordered w-full"></textarea>
                    @error('description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Team -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Team</span>
                        </label>
                        <select wire:model="team_id" class="select select-bordered w-full">
                            <option value="">Select team...</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Assign to</span>
                        </label>
                        <select wire:model="assigned_to" class="select select-bordered w-full">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('tenant.tickets.index', ['tenant' => $tenantSlug]) }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
