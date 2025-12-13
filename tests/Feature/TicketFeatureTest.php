<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Team $team;
    protected Department $department;

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
    }

    #[Test]
    public function authenticated_user_can_view_ticket_list(): void
    {
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Test Ticket',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/t/{$this->tenant->slug}/tickets");

        $response->assertStatus(200);
        $response->assertSee('Test Ticket');
    }

    #[Test]
    public function authenticated_user_can_create_ticket(): void
    {
        $this->actingAs($this->user);

        Livewire::test('tickets.create')
            ->set('type', 'incident')
            ->set('title', 'New Ticket Title')
            ->set('description', 'Ticket description')
            ->set('priority', 2)
            ->set('team_id', $this->team->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tickets', [
            'title' => 'New Ticket Title',
            'type' => 'incident',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    #[Test]
    public function ticket_list_shows_only_tenant_tickets(): void
    {
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'My Tenant Ticket',
            'created_by' => $this->user->id,
        ]);

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
        Ticket::withoutGlobalScopes()->create([
            'tenant_id' => $otherTenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Other Tenant Ticket',
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/t/{$this->tenant->slug}/tickets");

        $response->assertSee('My Tenant Ticket');
        $response->assertDontSee('Other Tenant Ticket');
    }

    #[Test]
    public function ticket_list_can_filter_by_status(): void
    {
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'New Status Ticket',
            'created_by' => $this->user->id,
        ]);

        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'in_progress',
            'title' => 'In Progress Ticket',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        Livewire::test('tickets.index')
            ->set('statusFilter', 'new')
            ->assertSee('New Status Ticket')
            ->assertDontSee('In Progress Ticket');
    }

    #[Test]
    public function ticket_list_can_search_by_title(): void
    {
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Email Problem',
            'created_by' => $this->user->id,
        ]);

        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'request',
            'status' => 'new',
            'title' => 'Laptop Request',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        Livewire::test('tickets.index')
            ->set('search', 'Email')
            ->assertSee('Email Problem')
            ->assertDontSee('Laptop Request');
    }

    #[Test]
    public function guest_cannot_access_ticket_list(): void
    {
        $response = $this->get("/t/{$this->tenant->slug}/tickets");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function dashboard_shows_ticket_statistics(): void
    {
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Ticket 1',
            'created_by' => $this->user->id,
        ]);

        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'request',
            'status' => 'in_progress',
            'title' => 'Ticket 2',
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        Livewire::test('dashboard.overview')
            ->assertSee('2')
            ->assertSee('1');
    }
}
