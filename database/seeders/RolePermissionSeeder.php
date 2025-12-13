<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = config('opshub.permissions');
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $tenantAdmin = Role::create(['name' => 'tenant_admin']);
        $tenantAdmin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo(['ticket.read', 'ticket.write', 'ticket.assign', 'audit.read']);

        $agent = Role::create(['name' => 'agent']);
        $agent->givePermissionTo(['ticket.read', 'ticket.write']);

        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo(['ticket.read']);
    }
}
