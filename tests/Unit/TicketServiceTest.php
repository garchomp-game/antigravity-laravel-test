<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Team $team;
    protected Department $department;
    protected TicketService $ticketService;

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

        $this->department = Department::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Department',
        ]);

        $this->team = Team::create([
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'name' => 'Test Team',
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->user->assignRole('agent');

        $this->ticketService = app(TicketService::class);
    }

    /** @test */
    public function it_can_create_a_ticket(): void
    {
        $this->actingAs($this->user);

        $ticket = $this->ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 2,
            'team_id' => $this->team->id,
            'department_id' => $this->department->id,
        ]);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals('Test Ticket', $ticket->title);
        $this->assertEquals('incident', $ticket->type);
        $this->assertEquals('new', $ticket->status);
        $this->assertEquals($this->user->id, $ticket->created_by);
        $this->assertEquals($this->tenant->id, $ticket->tenant_id);
    }

    /** @test */
    public function it_can_assign_a_ticket(): void
    {
        $this->actingAs($this->user);

        $ticket = $this->ticketService->createTicket([
            'type' => 'request',
            'title' => 'Assign Test',
        ]);

        $assignee = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Assignee',
            'email' => 'assignee@example.com',
            'password' => Hash::make('password'),
        ]);

        $updatedTicket = $this->ticketService->assignTicket($ticket, $assignee);

        $this->assertEquals($assignee->id, $updatedTicket->assigned_to);
    }

    /** @test */
    public function it_can_transition_ticket_status(): void
    {
        $this->actingAs($this->user);

        $ticket = $this->ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Status Test',
        ]);

        $this->assertEquals('new', $ticket->status);

        $updatedTicket = $this->ticketService->transitionStatus($ticket, 'in_progress');

        $this->assertEquals('in_progress', $updatedTicket->status);
    }

    /** @test */
    public function it_cannot_make_invalid_status_transition(): void
    {
        $this->actingAs($this->user);

        $ticket = $this->ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Invalid Transition Test',
        ]);

        // First transition to closed (which is allowed from new)
        $ticket = $this->ticketService->transitionStatus($ticket, 'closed');

        $this->expectException(\InvalidArgumentException::class);

        // 'closed' cannot transition to 'new' - this should throw
        $this->ticketService->transitionStatus($ticket, 'new');
    }

    /** @test */
    public function it_can_add_comment_to_ticket(): void
    {
        $this->actingAs($this->user);

        $ticket = $this->ticketService->createTicket([
            'type' => 'incident',
            'title' => 'Comment Test',
        ]);

        $event = $this->ticketService->addComment($ticket, 'This is a test comment');

        $this->assertEquals('comment', $event->kind);
        $this->assertEquals('This is a test comment', $event->body);
        $this->assertEquals($this->user->id, $event->actor_id);
    }
}
