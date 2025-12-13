<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Department;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Creating demo data...\n";

$tenant = Tenant::create([
    'name' => 'Demo Company',
    'slug' => 'demo',
    'is_active' => true,
]);
echo "Created tenant: {$tenant->id}\n";

Tenant::setCurrent($tenant);

$itDept = Department::create([
    'tenant_id' => $tenant->id,
    'name' => 'IT Department',
    'description' => 'Information Technology',
]);
echo "Created department: {$itDept->id}\n";

$supportTeam = Team::create([
    'tenant_id' => $tenant->id,
    'department_id' => $itDept->id,
    'name' => 'Support Team',
    'description' => 'IT Support',
]);
echo "Created team: {$supportTeam->id}\n";

$admin = User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Admin User',
    'email' => 'admin@demo.test',
    'password' => Hash::make('password'),
]);
$admin->assignRole('tenant_admin');
echo "Created admin user: {$admin->email}\n";

$agent = User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Agent User',
    'email' => 'agent@demo.test',
    'password' => Hash::make('password'),
]);
$agent->assignRole('agent');
echo "Created agent user: {$agent->email}\n";

$viewer = User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Viewer User',
    'email' => 'viewer@demo.test',
    'password' => Hash::make('password'),
]);
$viewer->assignRole('viewer');
echo "Created viewer user: {$viewer->email}\n";

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

echo "\nDone! Created " . User::count() . " users, " . Tenant::first()->name . "\n";
