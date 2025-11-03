<?php

namespace Bhhaskin\Tickets\Policies;

use Bhhaskin\Tickets\Models\Ticket;
use Illuminate\Contracts\Auth\Authenticatable;

class TicketPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket);
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    public function update(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket);
    }

    public function delete(Authenticatable $user, Ticket $ticket): bool
    {
        return false;
    }

    public function restore(Authenticatable $user, Ticket $ticket): bool
    {
        return false;
    }

    public function forceDelete(Authenticatable $user, Ticket $ticket): bool
    {
        return false;
    }

    public function reply(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket) && ! $ticket->isClosed();
    }

    protected function ownsTicket(Authenticatable $user, Ticket $ticket): bool
    {
        return (string) $ticket->user_id === (string) $user->getAuthIdentifier();
    }
}
