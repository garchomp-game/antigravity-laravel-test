<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected AuditService $auditService;

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

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->auditService = app(AuditService::class);
    }

    #[Test]
    public function it_can_record_an_audit_log(): void
    {
        $this->actingAs($this->user);

        $entityId = (string) Str::uuid();

        $auditLog = $this->auditService->record(
            'test.action',
            'TestEntity',
            $entityId,
            ['key' => 'value']
        );

        $this->assertInstanceOf(AuditLog::class, $auditLog);
        $this->assertEquals('test.action', $auditLog->action);
        $this->assertEquals('TestEntity', $auditLog->entity_type);
        $this->assertEquals($entityId, $auditLog->entity_id);
        $this->assertEquals(['key' => 'value'], $auditLog->meta);
        $this->assertEquals($this->user->id, $auditLog->actor_id);
        $this->assertEquals($this->tenant->id, $auditLog->tenant_id);
    }

    #[Test]
    public function it_records_audit_when_ticket_is_created(): void
    {
        $this->actingAs($this->user);

        $department = Department::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'IT Department',
        ]);

        $team = Team::create([
            'tenant_id' => $this->tenant->id,
            'department_id' => $department->id,
            'name' => 'Support Team',
        ]);

        $ticketService = app(TicketService::class);
        $ticket = $ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Audit Test Ticket',
            'team_id' => $team->id,
        ]);

        $auditLog = AuditLog::where('action', 'ticket.create')
            ->where('entity_id', $ticket->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('Ticket', $auditLog->entity_type);
        $this->assertEquals($ticket->id, $auditLog->entity_id);
        $this->assertEquals($this->user->id, $auditLog->actor_id);
        $this->assertArrayHasKey('title', $auditLog->meta);
    }

    #[Test]
    public function it_records_audit_when_ticket_is_assigned(): void
    {
        $this->actingAs($this->user);

        $assignee = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Assignee',
            'email' => 'assignee@example.com',
            'password' => Hash::make('password'),
        ]);

        $department = Department::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'IT Department',
        ]);

        $team = Team::create([
            'tenant_id' => $this->tenant->id,
            'department_id' => $department->id,
            'name' => 'Support Team',
        ]);

        $ticketService = app(TicketService::class);
        $ticket = $ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Assign Test Ticket',
        ]);

        $ticketService->assignTicket($ticket, $assignee);

        $auditLog = AuditLog::where('action', 'ticket.assign')
            ->where('entity_id', $ticket->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($ticket->id, $auditLog->entity_id);
        $this->assertArrayHasKey('new_assignee', $auditLog->meta);
        $this->assertEquals($assignee->id, $auditLog->meta['new_assignee']);
    }

    #[Test]
    public function it_records_audit_when_ticket_status_transitions(): void
    {
        $this->actingAs($this->user);

        $ticketService = app(TicketService::class);
        $ticket = $ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Status Test Ticket',
        ]);

        $ticketService->transitionStatus($ticket, 'in_progress');

        $auditLog = AuditLog::where('action', 'ticket.status.transition')
            ->where('entity_id', $ticket->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('new', $auditLog->meta['from_status']);
        $this->assertEquals('in_progress', $auditLog->meta['to_status']);
    }

    #[Test]
    public function it_records_audit_when_comment_is_added(): void
    {
        $this->actingAs($this->user);

        $ticketService = app(TicketService::class);
        $ticket = $ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Comment Test Ticket',
        ]);

        $ticketService->addComment($ticket, 'Test comment for audit');

        $auditLog = AuditLog::where('action', 'ticket.comment')
            ->where('entity_id', $ticket->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertArrayHasKey('comment_id', $auditLog->meta);
    }

    #[Test]
    public function audit_logs_are_scoped_to_tenant(): void
    {
        $this->actingAs($this->user);

        $entityId1 = (string) Str::uuid();
        $this->auditService->record('test.action', 'Entity', $entityId1);

        // Create another tenant
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other',
            'is_active' => true,
        ]);
        $otherUser = User::create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
        ]);

        Tenant::setCurrent($otherTenant);
        $this->actingAs($otherUser);

        $entityId2 = (string) Str::uuid();
        $this->auditService->record('test.action', 'Entity', $entityId2);

        // Switch back and verify scoping
        Tenant::setCurrent($this->tenant);
        $logs = AuditLog::all();

        $this->assertCount(1, $logs);
        $this->assertEquals($entityId1, $logs->first()->entity_id);
    }
}
