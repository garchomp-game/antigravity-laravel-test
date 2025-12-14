<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">User Management</h2>

        {{-- Search and Filter --}}
        <div class="flex flex-wrap gap-4 mb-4">
            <div class="flex-1 min-w-[200px]">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name or email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    data-testid="user-search-input"
                >
            </div>
            <div class="w-48">
                <select
                    wire:model.live="roleFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    data-testid="role-filter-select"
                >
                    <option value="">All Roles</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr data-testid="user-row-{{ $user->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $currentRole = $user->roles->first()?->name @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($currentRole === 'tenant_admin') bg-purple-100 text-purple-800
                                @elseif($currentRole === 'manager') bg-blue-100 text-blue-800
                                @elseif($currentRole === 'agent') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $roles[$currentRole] ?? ucfirst($currentRole ?? 'None') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if(auth()->id() !== $user->id)
                                <select
                                    wire:change="confirmRoleChange('{{ $user->id }}', $event.target.value)"
                                    class="text-sm border border-gray-300 rounded px-2 py-1"
                                    data-testid="role-select-{{ $user->id }}"
                                >
                                    <option value="" disabled selected>Change Role...</option>
                                    @foreach($roles as $roleKey => $roleLabel)
                                        @if($roleKey !== $currentRole)
                                            <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @else
                                <span class="text-gray-400 italic">Your account</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- Confirmation Modal --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" data-testid="confirm-modal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Role Change</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to change <strong>{{ $targetUserName }}</strong>'s role to
                    <strong>{{ $roles[$targetRole] ?? $targetRole }}</strong>?
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        wire:click="cancelRoleChange"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50"
                        data-testid="cancel-role-change"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="changeRole('{{ $targetUserId }}', '{{ $targetRole }}')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        data-testid="confirm-role-change"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Messages --}}
    @error('role')
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" data-testid="role-error">
            {{ $message }}
        </div>
    @enderror
</div>
