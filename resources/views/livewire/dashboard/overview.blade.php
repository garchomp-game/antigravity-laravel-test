<div>
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stat bg-base-100 shadow rounded-box">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="stat-title">Total Tickets</div>
            <div class="stat-value text-primary">{{ $totalTickets }}</div>
        </div>

        <div class="stat bg-base-100 shadow rounded-box">
            <div class="stat-figure text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="stat-title">My Tickets</div>
            <div class="stat-value text-secondary">{{ $myTickets }}</div>
        </div>

        <div class="stat bg-base-100 shadow rounded-box">
            <div class="stat-figure text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="stat-title">New</div>
            <div class="stat-value text-warning">{{ $statusCounts['new'] ?? 0 }}</div>
        </div>

        <div class="stat bg-base-100 shadow rounded-box">
            <div class="stat-figure text-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="stat-title">In Progress</div>
            <div class="stat-value text-info">{{ $statusCounts['in_progress'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">By Status</h2>
                <div class="space-y-2">
                    @foreach(config('opshub.ticket_statuses') as $key => $label)
                        <div class="flex justify-between items-center">
                            <span class="badge badge-outline">{{ $label }}</span>
                            <span class="font-mono">{{ $statusCounts[$key] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">By Type</h2>
                <div class="space-y-2">
                    @foreach(config('opshub.ticket_types') as $key => $label)
                        <div class="flex justify-between items-center">
                            <span class="badge badge-primary badge-outline">{{ $label }}</span>
                            <span class="font-mono">{{ $typeCounts[$key] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
