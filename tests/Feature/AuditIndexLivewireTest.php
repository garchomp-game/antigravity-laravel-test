<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditIndexLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $adminUser;

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
    }

    protected function createAuditLog(array $attrs = []): AuditLog
    {
        return AuditLog::create(array_merge([
            'tenant_id' => $this->tenant->id,
            'actor_id' => $this->adminUser->id,
            'action' => 'test.action',
            'entity_type' => 'Test',
            'entity_id' => fake()->uuid(),
            'request_id' => fake()->uuid(),
            'meta' => [],
        ], $attrs));
    }

    #[Test]
    public function renders_audit_log_list(): void
    {
        $this->createAuditLog([
            'action' => 'ticket.created',
            'meta' => ['title' => 'Test Ticket'],
        ]);

        $this->createAuditLog([
            'action' => 'user.role.changed',
            'meta' => ['old_role' => 'agent', 'new_role' => 'manager'],
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test('audit.index')
            ->assertSee('ticket.created')
            ->assertSee('user.role.changed')
            ->assertSee('Admin User');
    }

    #[Test]
    public function can_filter_by_action(): void
    {
        $log1 = $this->createAuditLog(['action' => 'unique.action.alpha']);
        $log2 = $this->createAuditLog(['action' => 'unique.action.beta']);

        $this->actingAs($this->adminUser);

        // Without filter - both rows visible via data-testid
        $component = Livewire::test('audit.index');
        $this->assertStringContainsString("audit-row-{$log1->id}", $component->html());
        $this->assertStringContainsString("audit-row-{$log2->id}", $component->html());

        // With filter - only alpha row visible
        $component->set('actionFilter', 'unique.action.alpha');
        $this->assertStringContainsString("audit-row-{$log1->id}", $component->html());
        $this->assertStringNotContainsString("audit-row-{$log2->id}", $component->html());
    }

    #[Test]
    public function can_filter_by_date_range(): void
    {
        $oldLog = $this->createAuditLog(['action' => 'old.dated.action']);
        // Update created_at directly in DB since it's not fillable
        AuditLog::where('id', $oldLog->id)->update(['created_at' => now()->subDays(30)]);
        $oldLog->refresh();

        $recentLog = $this->createAuditLog(['action' => 'recent.dated.action']);
        AuditLog::where('id', $recentLog->id)->update(['created_at' => now()->subDay()]);
        $recentLog->refresh();

        $this->actingAs($this->adminUser);

        $component = Livewire::test('audit.index')
            ->set('dateFrom', now()->subDays(7)->format('Y-m-d'))
            ->set('dateTo', now()->format('Y-m-d'));

        // Recent log row should be visible
        $this->assertStringContainsString("audit-row-{$recentLog->id}", $component->html());
        // Old log row should NOT be visible
        $this->assertStringNotContainsString("audit-row-{$oldLog->id}", $component->html());
    }

    #[Test]
    public function can_search_by_actor_name(): void
    {
        $otherUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Unique Other Person',
            'email' => 'other@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $otherUser->assignRole('agent');

        $adminLog = $this->createAuditLog(['action' => 'admin.specific.action', 'actor_id' => $this->adminUser->id]);
        $otherLog = $this->createAuditLog(['action' => 'other.specific.action', 'actor_id' => $otherUser->id]);

        $this->actingAs($this->adminUser);

        $component = Livewire::test('audit.index')
            ->set('actorSearch', 'Unique Other');

        // Other person's log should be visible
        $this->assertStringContainsString("audit-row-{$otherLog->id}", $component->html());
        // Admin's log should NOT be visible
        $this->assertStringNotContainsString("audit-row-{$adminLog->id}", $component->html());
    }

    #[Test]
    public function can_view_log_details(): void
    {
        $log = $this->createAuditLog([
            'action' => 'ticket.created',
            'ip' => '192.168.1.1',
            'user_agent' => 'Test Browser',
            'meta' => ['title' => 'Test Ticket', 'priority' => 'high'],
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test('audit.index')
            ->call('showDetails', $log->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedLogId', $log->id)
            ->assertSee('192.168.1.1')
            ->assertSee('Test Browser');
    }

    #[Test]
    public function paginates_results(): void
    {
        // Create 20 logs with distinct timestamps
        $logs = [];
        for ($i = 1; $i <= 20; $i++) {
            $logs[$i] = $this->createAuditLog([
                'action' => "paginated.action.{$i}",
                'created_at' => now()->subMinutes(21 - $i), // action.20 is most recent
            ]);
        }

        $this->actingAs($this->adminUser);

        $component = Livewire::test('audit.index');
        $html = $component->html();

        // Most recent (action.20) should be on first page
        $this->assertStringContainsString("audit-row-{$logs[20]->id}", $html);
        
        // Oldest (action.1 through 5) should NOT be on first page (15 per page)
        $this->assertStringNotContainsString("audit-row-{$logs[1]->id}", $html);
        $this->assertStringNotContainsString("audit-row-{$logs[5]->id}", $html);
    }
}
