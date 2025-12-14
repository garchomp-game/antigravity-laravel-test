<?php

namespace App\Livewire\Admin\Users;

use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    // Confirmation modal state
    public bool $showConfirmModal = false;
    public ?string $targetUserId = null;
    public ?string $targetRole = null;
    public ?string $targetUserName = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function confirmRoleChange(string $userId, string $newRole): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $this->targetUserId = $userId;
        $this->targetRole = $newRole;
        $this->targetUserName = $user->name;
        $this->showConfirmModal = true;
    }

    public function cancelRoleChange(): void
    {
        $this->showConfirmModal = false;
        $this->targetUserId = null;
        $this->targetRole = null;
        $this->targetUserName = null;
    }

    public function changeRole(string $userId, string $newRole): void
    {
        $currentUser = auth()->user();
        $targetUser = User::where('id', $userId)
            ->where('tenant_id', Tenant::current()?->id)
            ->first();

        if (!$targetUser) {
            $this->addError('role', 'User not found');
            return;
        }

        // Cannot change own role
        if ($currentUser->id === $targetUser->id) {
            $this->addError('role', 'Cannot change your own role');
            return;
        }

        // Check if demoting last admin
        if ($targetUser->hasRole('tenant_admin') && $newRole !== 'tenant_admin') {
            $adminCount = User::role('tenant_admin')
                ->where('tenant_id', Tenant::current()?->id)
                ->count();

            if ($adminCount <= 1) {
                $this->addError('role', 'Cannot demote the last tenant administrator');
                return;
            }
        }

        // Get old role for audit
        $oldRole = $targetUser->roles->first()?->name ?? 'none';

        // Remove all current roles and assign new one
        $targetUser->syncRoles([$newRole]);

        // Record audit log
        $auditService = app(AuditService::class);
        $auditService->record(
            'user.role.changed',
            'User',
            $targetUser->id,
            [
                'target_user_id' => $targetUser->id,
                'target_user_email' => $targetUser->email,
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ]
        );

        // Close modal
        $this->cancelRoleChange();

        // Dispatch browser event for notification
        $this->dispatch('role-changed', name: $targetUser->name, role: $newRole);
    }

    public function render()
    {
        $tenant = Tenant::current();

        $usersQuery = User::query()
            ->where('tenant_id', $tenant?->id)
            ->with('roles');

        // Apply search filter
        if ($this->search) {
            $usersQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply role filter
        if ($this->roleFilter) {
            $usersQuery->role($this->roleFilter);
        }

        $users = $usersQuery->orderBy('name')->paginate(15);

        $roles = config('opshub.roles');

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
