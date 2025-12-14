<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTenantRelationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    #[Test]
    public function user_belongs_to_primary_tenant(): void
    {
        $tenant = Tenant::create([
            'name' => 'Primary Tenant',
            'slug' => 'primary',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertNotNull($user->tenant);
        $this->assertEquals($tenant->id, $user->tenant->id);
    }

    #[Test]
    public function user_can_belong_to_multiple_tenants(): void
    {
        $tenant1 = Tenant::create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Multi Tenant User',
            'email' => 'multi@example.com',
            'password' => Hash::make('password'),
        ]);

        // Attach user to multiple tenants
        $user->tenants()->attach($tenant1->id, ['is_primary' => true]);
        $user->tenants()->attach($tenant2->id, ['is_primary' => false]);

        $this->assertCount(2, $user->tenants);
        $this->assertTrue($user->tenants->contains($tenant1));
        $this->assertTrue($user->tenants->contains($tenant2));
    }

    #[Test]
    public function user_can_access_tenant_returns_true_for_member(): void
    {
        $tenant = Tenant::create([
            'name' => 'Member Tenant',
            'slug' => 'member-tenant',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->tenants()->attach($tenant->id);

        $this->assertTrue($user->canAccessTenant($tenant));
    }

    #[Test]
    public function user_can_access_tenant_returns_false_for_non_member(): void
    {
        $tenant1 = Tenant::create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Single Tenant User',
            'email' => 'single@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->tenants()->attach($tenant1->id);

        $this->assertTrue($user->canAccessTenant($tenant1));
        $this->assertFalse($user->canAccessTenant($tenant2));
    }
}
