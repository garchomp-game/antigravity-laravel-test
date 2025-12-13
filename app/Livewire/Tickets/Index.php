<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Ticket::query()
            ->with(['creator', 'assignee', 'team'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'ilike', '%' . $this->search . '%')
                  ->orWhere('description', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        return view('livewire.tickets.index', [
            'tickets' => $query->paginate(10),
            'statuses' => config('opshub.ticket_statuses'),
            'types' => config('opshub.ticket_types'),
            'tenantSlug' => \App\Models\Tenant::current()?->slug,
        ]);
    }
}
