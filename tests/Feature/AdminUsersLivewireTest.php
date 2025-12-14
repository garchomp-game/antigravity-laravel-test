<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUsersLivewireTest extends TestCase
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
    public function renders_users_list_for_same_tenant(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->assertSee('Admin User')
            ->assertSee('admin@test.example.com')
            ->assertSee('Manager User')
            ->assertSee('Agent User')
            ->assertSee('Viewer User');
    }

    #[Test]
    public function does_not_show_users_from_other_tenants(): void
    {
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other',
            'is_active' => true,
        ]);

        $otherUser = User::create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Other Tenant User',
            'email' => 'other@other.example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->assertSee('Admin User')
            ->assertDontSee('Other Tenant User')
            ->assertDontSee('other@other.example.com');
    }

    #[Test]
    public function can_search_users_by_name(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->set('search', 'Manager')
            ->assertSee('Manager User')
            ->assertDontSee('Agent User')
            ->assertDontSee('Viewer User');
    }

    #[Test]
    public function can_search_users_by_email(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->set('search', 'agent@')
            ->assertSee('Agent User')
            ->assertDontSee('Manager User')
            ->assertDontSee('Viewer User');
    }

    #[Test]
    public function can_filter_users_by_role(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->set('roleFilter', 'manager')
            ->assertSee('Manager User')
            ->assertDontSee('Agent User')
            ->assertDontSee('Viewer User');
    }

    #[Test]
    public function can_change_user_role(): void
    {
        $this->actingAs($this->adminUser);

        $this->assertTrue($this->agentUser->hasRole('agent'));

        Livewire::test('admin.users.index')
            ->call('changeRole', $this->agentUser->id, 'manager')
            ->assertHasNoErrors();

        $this->agentUser->refresh();
        $this->assertTrue($this->agentUser->hasRole('manager'));
        $this->assertFalse($this->agentUser->hasRole('agent'));
    }

    #[Test]
    public function shows_confirmation_before_role_change(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test('admin.users.index')
            ->call('confirmRoleChange', $this->agentUser->id, 'manager')
            ->assertSet('showConfirmModal', true)
            ->assertSet('targetUserId', $this->agentUser->id)
            ->assertSet('targetRole', 'manager');
    }
}
