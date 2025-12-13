<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Create a new ticket.
     */
    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'tenant_id' => Tenant::current()?->id,
                'type' => $data['type'],
                'status' => 'new',
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? null,
                'created_by' => auth()->id(),
                'assigned_to' => $data['assigned_to'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'team_id' => $data['team_id'] ?? null,
            ]);

            // Create initial event
            TicketEvent::create([
                'tenant_id' => $ticket->tenant_id,
                'ticket_id' => $ticket->id,
                'kind' => TicketEvent::KIND_SYSTEM,
                'body' => 'Ticket created',
                'actor_id' => auth()->id(),
            ]);

            // Audit log
            $this->auditService->record(
                'ticket.create',
                'Ticket',
                $ticket->id,
                ['title' => $ticket->title, 'type' => $ticket->type]
            );

            return $ticket;
        });
    }

    /**
     * Assign a ticket to a user.
     */
    public function assignTicket(Ticket $ticket, ?\App\Models\User $user): Ticket
    {
        return DB::transaction(function () use ($ticket, $user) {
            $oldAssignee = $ticket->assigned_to;
            $ticket->assigned_to = $user?->id;
            $ticket->save();

            // Create assignment event
            TicketEvent::create([
                'tenant_id' => $ticket->tenant_id,
                'ticket_id' => $ticket->id,
                'kind' => TicketEvent::KIND_ASSIGNMENT,
                'body' => $user ? 'Ticket assigned' : 'Ticket unassigned',
                'actor_id' => auth()->id(),
            ]);

            // Audit log
            $this->auditService->record(
                'ticket.assign',
                'Ticket',
                $ticket->id,
                ['old_assignee' => $oldAssignee, 'new_assignee' => $user?->id]
            );

            return $ticket;
        });
    }

    /**
     * Transition ticket status.
     */
    public function transitionStatus(Ticket $ticket, string $newStatus, ?string $comment = null): Ticket
    {
        if (!$ticket->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$ticket->status} to {$newStatus}"
            );
        }

        return DB::transaction(function () use ($ticket, $newStatus, $comment) {
            $oldStatus = $ticket->status;
            $ticket->status = $newStatus;
            $ticket->save();

            // Create status change event
            TicketEvent::create([
                'tenant_id' => $ticket->tenant_id,
                'ticket_id' => $ticket->id,
                'kind' => TicketEvent::KIND_STATUS_CHANGE,
                'body' => $comment,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'actor_id' => auth()->id(),
            ]);

            // Audit log
            $this->auditService->record(
                'ticket.status.transition',
                'Ticket',
                $ticket->id,
                ['from_status' => $oldStatus, 'to_status' => $newStatus]
            );

            return $ticket;
        });
    }

    /**
     * Add a comment to a ticket.
     */
    public function addComment(Ticket $ticket, string $body): TicketEvent
    {
        $event = TicketEvent::create([
            'tenant_id' => $ticket->tenant_id,
            'ticket_id' => $ticket->id,
            'kind' => TicketEvent::KIND_COMMENT,
            'body' => $body,
            'actor_id' => auth()->id(),
        ]);

        $this->auditService->record(
            'ticket.comment',
            'Ticket',
            $ticket->id,
            ['comment_id' => $event->id]
        );

        return $event;
    }
}
