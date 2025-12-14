<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditIndexTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $adminUser;
    protected User $managerUser;
    protected User $agentUser;

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
    }

    #[Test]
    public function admin_can_access_audit_page(): void
    {
        // Create some audit logs
        AuditLog::create([
            'tenant_id' => $this->tenant->id,
            'actor_id' => $this->adminUser->id,
            'action' => 'ticket.created',
            'entity_type' => 'Ticket',
            'entity_id' => fake()->uuid(),
            'request_id' => fake()->uuid(),
            'meta' => ['title' => 'Test Ticket'],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->get("/t/{$this->tenant->slug}/audit");

        $response->assertStatus(200);
        $response->assertSee('ticket.created');
    }

    #[Test]
    public function manager_can_access_audit_page(): void
    {
        // Manager has audit.read permission
        $response = $this
            ->actingAs($this->managerUser)
            ->get("/t/{$this->tenant->slug}/audit");

        $response->assertStatus(200);
    }

    #[Test]
    public function agent_cannot_access_audit_page(): void
    {
        // Agent does NOT have audit.read permission
        $response = $this
            ->actingAs($this->agentUser)
            ->get("/t/{$this->tenant->slug}/audit");

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_access_other_tenant_audit(): void
    {
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->get("/t/{$otherTenant->slug}/audit");

        $response->assertStatus(404);
    }

    #[Test]
    public function audit_logs_are_scoped_to_tenant(): void
    {
        // Create audit log for current tenant
        AuditLog::create([
            'tenant_id' => $this->tenant->id,
            'actor_id' => $this->adminUser->id,
            'action' => 'ticket.created',
            'entity_type' => 'Ticket',
            'entity_id' => fake()->uuid(),
            'request_id' => fake()->uuid(),
            'meta' => ['title' => 'My Tenant Ticket'],
        ]);

        // Create audit log for other tenant
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other',
            'is_active' => true,
        ]);
        AuditLog::create([
            'tenant_id' => $otherTenant->id,
            'actor_id' => $this->adminUser->id,
            'action' => 'ticket.created',
            'entity_type' => 'Ticket',
            'entity_id' => fake()->uuid(),
            'request_id' => fake()->uuid(),
            'meta' => ['title' => 'Other Tenant Ticket'],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->get("/t/{$this->tenant->slug}/audit");

        $response->assertStatus(200);
        $response->assertSee('My Tenant Ticket');
        $response->assertDontSee('Other Tenant Ticket');
    }
}
