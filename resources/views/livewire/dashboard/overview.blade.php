<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-base-content">
                Dashboard
            </h1>
            <p class="text-base-content/60 mt-1">
                Overview of your tenant activity.
            </p>
        </div>
        <div class="text-sm font-medium text-base-content/50">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Tickets -->
        <div class="card bg-gradient-to-br from-primary to-secondary text-primary-content shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-primary-content/80 text-sm font-medium uppercase tracking-wider mb-1">Total Tickets</div>
                        <div class="text-4xl font-bold">{{ $totalTickets }}</div>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs font-medium text-primary-content/70">
                    All time tickets
                </div>
            </div>
        </div>

        <!-- My Tickets -->
        <div class="card bg-base-100 shadow-xl border border-base-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-base-content/60 text-sm font-medium uppercase tracking-wider mb-1">My Tickets</div>
                        <div class="text-4xl font-bold text-secondary">{{ $myTickets }}</div>
                    </div>
                    <div class="p-3 bg-secondary/10 text-secondary rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs font-medium text-base-content/50">
                    Assigned to you
                </div>
            </div>
        </div>

        <!-- New Tickets -->
        <div class="card bg-base-100 shadow-xl border border-base-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-base-content/60 text-sm font-medium uppercase tracking-wider mb-1">New</div>
                        <div class="text-4xl font-bold text-warning">{{ $statusCounts['new'] ?? 0 }}</div>
                    </div>
                    <div class="p-3 bg-warning/10 text-warning rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs font-medium text-base-content/50">
                    Needs attention
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="card bg-base-100 shadow-xl border border-base-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-base-content/60 text-sm font-medium uppercase tracking-wider mb-1">In Progress</div>
                        <div class="text-4xl font-bold text-info">{{ $statusCounts['in_progress'] ?? 0 }}</div>
                    </div>
                    <div class="p-3 bg-info/10 text-info rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs font-medium text-base-content/50">
                    Active tasks
                </div>
            </div>
        </div>
    </div>

    <!-- Charts / Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- By Status -->
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">Tickets by Status</h2>
                <div class="space-y-4">
                    @foreach(config('opshub.ticket_statuses') as $key => $label)
                        <div class="flex items-center gap-4 group">
                            <div class="w-32 flex-shrink-0 text-sm font-medium text-base-content/70 group-hover:text-primary transition-colors">{{ $label }}</div>
                            <div class="flex-grow bg-base-200 rounded-full h-2.5 overflow-hidden">
                                @php
                                    $count = $statusCounts[$key] ?? 0;
                                    $percentage = $totalTickets > 0 ? ($count / $totalTickets) * 100 : 0;
                                    $colorClass = match($key) {
                                        'new' => 'bg-warning',
                                        'in_progress' => 'bg-info',
                                        'resolved' => 'bg-success',
                                        'closed' => 'bg-neutral',
                                        default => 'bg-primary'
                                    };
                                @endphp
                                <div class="{{ $colorClass }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-12 text-right font-mono text-sm font-bold">{{ $count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- By Type -->
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">Tickets by Type</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(config('opshub.ticket_types') as $key => $label)
                        <div class="p-4 rounded-xl bg-base-200/50 hover:bg-base-200 transition-colors border border-transparent hover:border-base-300">
                            <div class="text-sm text-base-content/60 mb-1">{{ $label }}</div>
                            <div class="text-2xl font-bold flex items-center gap-2">
                                {{ $typeCounts[$key] ?? 0 }}
                                <span class="text-xs font-normal text-base-content/40">tickets</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
