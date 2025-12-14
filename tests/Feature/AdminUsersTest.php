<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $adminUser;
    protected User $managerUser;
    protected User $agentUser;
    protected User $viewerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test',
            'is_active' => true,
        ]);
        Tenant::setCurrent($this->tenant);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->adminUser->assignRole('tenant_admin');

        $this->managerUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Manager User',
            'email' => 'manager@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->managerUser->assignRole('manager');

        $this->agentUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Agent User',
            'email' => 'agent@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->agentUser->assignRole('agent');

        $this->viewerUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Viewer User',
            'email' => 'viewer@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->viewerUser->assignRole('viewer');
    }

    #[Test]
    public function tenant_admin_can_access_admin_users_page(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->get("/t/{$this->tenant->slug}/admin/users");

        $response->assertStatus(200);
        $response->assertSee('Admin User');
        $response->assertSee('Manager User');
        $response->assertSee('Agent User');
        $response->assertSee('Viewer User');
    }

    #[Test]
    public function non_admin_cannot_access_admin_users_page(): void
    {
        // Manager cannot access
        $response = $this
            ->actingAs($this->managerUser)
            ->get("/t/{$this->tenant->slug}/admin/users");
        $response->assertStatus(403);

        // Agent cannot access
        $response = $this
            ->actingAs($this->agentUser)
            ->get("/t/{$this->tenant->slug}/admin/users");
        $response->assertStatus(403);

        // Viewer cannot access
        $response = $this
            ->actingAs($this->viewerUser)
            ->get("/t/{$this->tenant->slug}/admin/users");
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_access_other_tenant_users(): void
    {
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other',
            'is_active' => true,
        ]);

        // Tenant A admin tries to access Tenant B's users page
        $response = $this
            ->actingAs($this->adminUser)
            ->get("/t/{$otherTenant->slug}/admin/users");

        // Should be 404 (tenant boundary) not 403 (permission)
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_change_user_role_with_audit_log(): void
    {
        $this->actingAs($this->adminUser);

        // Change agent to manager via Livewire
        \Livewire\Livewire::test('admin.users.index')
            ->call('changeRole', $this->agentUser->id, 'manager')
            ->assertHasNoErrors();

        // Verify role changed
        $this->agentUser->refresh();
        $this->assertTrue($this->agentUser->hasRole('manager'));
        $this->assertFalse($this->agentUser->hasRole('agent'));

        // Verify AuditLog was created
        $auditLog = AuditLog::where('action', 'user.role.changed')
            ->where('entity_id', $this->agentUser->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($this->adminUser->id, $auditLog->actor_id);
        $this->assertEquals($this->tenant->id, $auditLog->tenant_id);
        $this->assertEquals('agent', $auditLog->meta['old_role']);
        $this->assertEquals('manager', $auditLog->meta['new_role']);
    }

    #[Test]
    public function cannot_demote_last_tenant_admin(): void
    {
        $this->actingAs($this->adminUser);

        // Try to demote the only admin to agent
        \Livewire\Livewire::test('admin.users.index')
            ->call('changeRole', $this->adminUser->id, 'agent')
            ->assertHasErrors(['role']);

        // Admin should still be admin
        $this->adminUser->refresh();
        $this->assertTrue($this->adminUser->hasRole('tenant_admin'));
    }

    #[Test]
    public function cannot_change_own_role(): void
    {
        $this->actingAs($this->adminUser);

        // Admin tries to change their own role
        \Livewire\Livewire::test('admin.users.index')
            ->call('changeRole', $this->adminUser->id, 'agent')
            ->assertHasErrors(['role']);
    }
}
