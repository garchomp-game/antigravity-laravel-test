<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ticket Types
    |--------------------------------------------------------------------------
    */
    'ticket_types' => [
        'incident' => 'Incident',
        'request' => 'Request',
        'change' => 'Change',
        'task' => 'Task',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Statuses
    |--------------------------------------------------------------------------
    */
    'ticket_statuses' => [
        'new' => 'New',
        'triaged' => 'Triaged',
        'in_progress' => 'In Progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Transitions (allowed next statuses)
    |--------------------------------------------------------------------------
    */
    'status_transitions' => [
        'new' => ['triaged', 'in_progress', 'closed'],
        'triaged' => ['in_progress', 'pending', 'closed'],
        'in_progress' => ['pending', 'resolved', 'closed'],
        'pending' => ['in_progress', 'resolved', 'closed'],
        'resolved' => ['closed', 'in_progress'],
        'closed' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Priorities
    |--------------------------------------------------------------------------
    */
    'priorities' => [
        1 => 'Critical',
        2 => 'High',
        3 => 'Medium',
        4 => 'Low',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'tenant_admin' => 'Tenant Administrator',
        'manager' => 'Manager',
        'agent' => 'Agent',
        'viewer' => 'Viewer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'ticket.read',
        'ticket.write',
        'ticket.delete',
        'ticket.assign',
        'admin.users.manage',
        'admin.roles.manage',
        'admin.departments.manage',
        'admin.teams.manage',
        'audit.read',
    ],
];
