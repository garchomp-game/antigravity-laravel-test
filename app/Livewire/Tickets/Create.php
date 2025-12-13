<?php

namespace App\Livewire\Tickets;

use App\Models\Team;
use App\Models\User;
use App\Services\TicketService;
use Livewire\Component;

class Create extends Component
{
    public string $type = 'incident';
    public string $title = '';
    public string $description = '';
    public ?int $priority = 3;
    public ?string $team_id = null;
    public ?string $assigned_to = null;

    protected function rules(): array
    {
        return [
            'type' => 'required|in:incident,request,change,task',
            'title' => 'required|string|min:5|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'nullable|integer|between:1,4',
            'team_id' => 'nullable|uuid|exists:teams,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
        ];
    }

    public function save(TicketService $ticketService): void
    {
        $this->validate();

        $ticket = $ticketService->createTicket([
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'team_id' => $this->team_id,
            'assigned_to' => $this->assigned_to,
        ]);

        session()->flash('message', 'Ticket created successfully.');

        $this->redirect(route('tenant.tickets.show', [
            'tenant' => \App\Models\Tenant::current()?->slug,
            'ticket' => $ticket->id,
        ]));
    }

    public function render()
    {
        return view('livewire.tickets.create', [
            'types' => config('opshub.ticket_types'),
            'priorities' => config('opshub.priorities'),
            'teams' => Team::all(),
            'users' => User::all(),
            'tenantSlug' => \App\Models\Tenant::current()?->slug,
        ]);
    }
}
