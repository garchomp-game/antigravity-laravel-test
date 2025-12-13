<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Livewire\Component;

class Show extends Component
{
    public Ticket $ticket;
    public string $newComment = '';
    public ?string $selectedAssignee = null;

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->load(['creator', 'assignee', 'team', 'events.actor']);
        $this->selectedAssignee = $ticket->assigned_to;
    }

    public function addComment(TicketService $ticketService): void
    {
        $this->validate([
            'newComment' => 'required|string|min:1|max:5000',
        ]);

        $ticketService->addComment($this->ticket, $this->newComment);

        $this->newComment = '';
        $this->ticket->refresh();
        $this->ticket->load(['events.actor']);
    }

    public function transitionStatus(string $newStatus, TicketService $ticketService): void
    {
        $ticketService->transitionStatus($this->ticket, $newStatus);

        $this->ticket->refresh();
        $this->ticket->load(['events.actor']);
    }

    public function assignTicket(TicketService $ticketService): void
    {
        $assignee = $this->selectedAssignee 
            ? User::find($this->selectedAssignee) 
            : null;

        $ticketService->assignTicket($this->ticket, $assignee);

        $this->ticket->refresh();
    }

    public function render()
    {
        $allowedTransitions = config('opshub.status_transitions')[$this->ticket->status] ?? [];

        return view('livewire.tickets.show', [
            'events' => $this->ticket->events()->with('actor')->orderBy('created_at', 'desc')->get(),
            'allowedTransitions' => $allowedTransitions,
            'statuses' => config('opshub.ticket_statuses'),
            'users' => User::all(),
            'tenantSlug' => \App\Models\Tenant::current()?->slug,
        ]);
    }
}
