<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TicketShowTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Team $team;
    protected Department $department;
    protected Ticket $ticket;

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
            'name' => 'IT Department',
        ]);

        $this->team = Team::create([
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'name' => 'Support Team',
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->user->assignRole('agent');

        $this->ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 2,
            'created_by' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_ticket_detail(): void
    {
        $response = $this->actingAs($this->user)
            ->get("/t/{$this->tenant->slug}/tickets/{$this->ticket->id}");

        $response->assertStatus(200);
        $response->assertSee('Test Ticket');
        $response->assertSee('Test description');
    }

    /** @test */
    public function ticket_show_displays_ticket_events(): void
    {
        TicketEvent::create([
            'tenant_id' => $this->tenant->id,
            'ticket_id' => $this->ticket->id,
            'kind' => 'comment',
            'body' => 'This is a test comment',
            'actor_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        Livewire::test('tickets.show', ['ticket' => $this->ticket])
            ->assertSee('This is a test comment');
    }

    /** @test */
    public function user_can_add_comment_to_ticket(): void
    {
        $this->actingAs($this->user);

        Livewire::test('tickets.show', ['ticket' => $this->ticket])
            ->set('newComment', 'My new comment')
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_events', [
            'ticket_id' => $this->ticket->id,
            'kind' => 'comment',
            'body' => 'My new comment',
        ]);
    }

    /** @test */
    public function user_can_change_ticket_status(): void
    {
        $this->actingAs($this->user);

        Livewire::test('tickets.show', ['ticket' => $this->ticket])
            ->call('transitionStatus', 'in_progress')
            ->assertHasNoErrors();

        $this->ticket->refresh();
        $this->assertEquals('in_progress', $this->ticket->status);
    }

    /** @test */
    public function user_can_assign_ticket(): void
    {
        $assignee = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Assignee User',
            'email' => 'assignee@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($this->user);

        Livewire::test('tickets.show', ['ticket' => $this->ticket])
            ->set('selectedAssignee', $assignee->id)
            ->call('assignTicket')
            ->assertHasNoErrors();

        $this->ticket->refresh();
        $this->assertEquals($assignee->id, $this->ticket->assigned_to);
    }

    /** @test */
    public function guest_cannot_view_ticket_detail(): void
    {
        $response = $this->get("/t/{$this->tenant->slug}/tickets/{$this->ticket->id}");

        $response->assertRedirect('/login');
    }
}
