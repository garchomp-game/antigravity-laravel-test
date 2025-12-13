<?php

namespace App\Livewire\Dashboard;

use App\Models\Ticket;
use Livewire\Component;

class Overview extends Component
{
    public function render()
    {
        $totalTickets = Ticket::count();
        $myTickets = Ticket::where('assigned_to', auth()->id())->count();

        $statusCounts = Ticket::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $typeCounts = Ticket::query()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('livewire.dashboard.overview', [
            'totalTickets' => $totalTickets,
            'myTickets' => $myTickets,
            'statusCounts' => $statusCounts,
            'typeCounts' => $typeCounts,
        ]);
    }
}
