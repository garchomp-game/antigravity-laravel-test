<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantSwitchingTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant1;
    protected Tenant $tenant2;
    protected User $multiTenantUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->tenant1 = Tenant::create([
            'name' => 'Tenant Alpha',
            'slug' => 'alpha',
            'is_active' => true,
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Tenant Beta',
            'slug' => 'beta',
            'is_active' => true,
        ]);

        $this->multiTenantUser = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Multi Tenant User',
            'email' => 'multi@test.example.com',
            'password' => Hash::make('password'),
        ]);
        $this->multiTenantUser->assignRole('tenant_admin');

        // Attach user to both tenants
        $this->multiTenantUser->tenants()->attach($this->tenant1->id, ['is_primary' => true]);
        $this->multiTenantUser->tenants()->attach($this->tenant2->id, ['is_primary' => false]);
    }

    #[Test]
    public function current_tenant_displays_in_nav(): void
    {
        $this->actingAs($this->multiTenantUser);

        $response = $this->get("/t/{$this->tenant1->slug}/dashboard");

        $response->assertStatus(200);
        $response->assertSee('Tenant Alpha');
    }

    #[Test]
    public function tenant_switcher_dropdown_shows_user_tenants(): void
    {
        $this->actingAs($this->multiTenantUser);

        $response = $this->get("/t/{$this->tenant1->slug}/dashboard");

        $response->assertStatus(200);
        // Check for data-testid attributes for tenant switcher
        $response->assertSee('data-testid="tenant-switcher"', false);
        $response->assertSee('Tenant Alpha');
        $response->assertSee('Tenant Beta');
    }

    #[Test]
    public function can_switch_to_another_tenant(): void
    {
        $this->actingAs($this->multiTenantUser);

        // Access tenant2 (user is a member)
        $response = $this->get("/t/{$this->tenant2->slug}/dashboard");

        $response->assertStatus(200);
        $response->assertSee('Tenant Beta');
    }

    #[Test]
    public function cannot_switch_to_non_member_tenant_404(): void
    {
        $tenant3 = Tenant::create([
            'name' => 'Tenant Gamma',
            'slug' => 'gamma',
            'is_active' => true,
        ]);

        $this->actingAs($this->multiTenantUser);

        $response = $this->get("/t/{$tenant3->slug}/dashboard");

        $response->assertStatus(404);
    }

    #[Test]
    public function cannot_access_nonexistent_tenant_404(): void
    {
        $this->actingAs($this->multiTenantUser);

        $response = $this->get('/t/nonexistent-slug/dashboard');

        $response->assertStatus(404);
    }

    #[Test]
    public function unauthenticated_redirects_to_login(): void
    {
        $response = $this->get("/t/{$this->tenant1->slug}/dashboard");

        $response->assertRedirect('/login');
    }
}
