<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'name' => 'Demo Company',
            'slug' => 'demo',
            'is_active' => true,
        ]);

        Tenant::setCurrent($tenant);

        $itDept = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'IT Department',
            'description' => 'Information Technology',
        ]);

        $supportTeam = Team::create([
            'tenant_id' => $tenant->id,
            'department_id' => $itDept->id,
            'name' => 'Support Team',
            'description' => 'IT Support',
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('tenant_admin');

        $agent = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Agent User',
            'email' => 'agent@demo.test',
            'password' => Hash::make('password'),
        ]);
        $agent->assignRole('agent');

        $viewer = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Viewer User',
            'email' => 'viewer@demo.test',
            'password' => Hash::make('password'),
        ]);
        $viewer->assignRole('viewer');

        Ticket::create([
            'tenant_id' => $tenant->id,
            'type' => 'incident',
            'status' => 'new',
            'title' => 'Cannot access email',
            'description' => 'Email not working since this morning.',
            'priority' => 2,
            'created_by' => $admin->id,
            'team_id' => $supportTeam->id,
            'department_id' => $itDept->id,
        ]);
    }
}
