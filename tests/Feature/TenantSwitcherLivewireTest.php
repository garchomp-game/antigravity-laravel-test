<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantSwitcherLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant1;
    protected Tenant $tenant2;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->tenant1 = Tenant::create([
            'name' => 'Primary Corp',
            'slug' => 'primary-corp',
            'is_active' => true,
        ]);
        Tenant::setCurrent($this->tenant1);

        $this->tenant2 = Tenant::create([
            'name' => 'Secondary Inc',
            'slug' => 'secondary-inc',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Test User',
            'email' => 'user@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->user->assignRole('tenant_admin');

        // Multi-tenant membership
        $this->user->tenants()->attach($this->tenant1->id, ['is_primary' => true]);
        $this->user->tenants()->attach($this->tenant2->id, ['is_primary' => false]);
    }

    #[Test]
    public function renders_current_tenant_name(): void
    {
        $this->actingAs($this->user);

        Livewire::test('tenant-switcher', ['currentTenantSlug' => $this->tenant1->slug])
            ->assertSee('Primary Corp');
    }

    #[Test]
    public function renders_tenant_dropdown_with_all_user_tenants(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test('tenant-switcher', ['currentTenantSlug' => $this->tenant1->slug]);

        $html = $component->html();
        $this->assertStringContainsString('data-testid="tenant-switcher"', $html);
        $this->assertStringContainsString('Primary Corp', $html);
        $this->assertStringContainsString('Secondary Inc', $html);
    }

    #[Test]
    public function current_tenant_is_highlighted(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test('tenant-switcher', ['currentTenantSlug' => $this->tenant1->slug]);

        $html = $component->html();
        // Check for current tenant indicator (checkmark or active class)
        $this->assertStringContainsString('data-testid="tenant-option-primary-corp"', $html);
        $this->assertStringContainsString('data-testid="current-tenant-indicator"', $html);
    }

    #[Test]
    public function selecting_tenant_redirects(): void
    {
        $this->actingAs($this->user);

        Livewire::test('tenant-switcher', ['currentTenantSlug' => $this->tenant1->slug])
            ->call('switchTo', 'secondary-inc')
            ->assertRedirect('/t/secondary-inc/dashboard');
    }

    #[Test]
    public function has_required_data_testid_attributes(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test('tenant-switcher', ['currentTenantSlug' => $this->tenant1->slug]);

        $html = $component->html();
        $this->assertStringContainsString('data-testid="tenant-switcher"', $html);
        $this->assertStringContainsString('data-testid="tenant-dropdown-trigger"', $html);
        $this->assertStringContainsString('data-testid="tenant-dropdown-menu"', $html);
    }
}
