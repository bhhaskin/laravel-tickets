<?php

namespace Bhhaskin\Tickets\Policies;

use Bhhaskin\Tickets\Models\Ticket;
use Illuminate\Contracts\Auth\Authenticatable;

class TicketPolicy
{
    /**
     * Override all checks for admin/support users.
     * Uncomment and customize based on your user model's role system.
     */
    public function before(Authenticatable $user, string $ability): ?bool
    {
        // Example implementations - uncomment and adjust for your needs:

        // If using a hasRole method:
        // if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
        //     return true;
        // }

        // If using a role attribute:
        // if (property_exists($user, 'role') && in_array($user->role, ['admin', 'support'])) {
        //     return true;
        // }

        // If using Spatie permissions:
        // if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('manage-all-tickets')) {
        //     return true;
        // }

        return null;
    }

    /**
     * Determine if user can view any tickets.
     * By default, authenticated users can only view their own tickets via the view() method.
     * Override this to allow viewing all tickets for specific roles.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Only allow viewing the index - actual viewing is controlled by view() method
        return true;
    }

    /**
     * Determine if user can view a specific ticket.
     */
    public function view(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket) || $this->canAccessWorkspace($user, $ticket);
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine if user can update a ticket.
     */
    public function update(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket) || $this->canAccessWorkspace($user, $ticket);
    }

    /**
     * Determine if user can delete a ticket.
     * By default, regular users cannot delete tickets.
     * Override the before() method to allow admins.
     */
    public function delete(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket);
    }

    public function restore(Authenticatable $user, Ticket $ticket): bool
    {
        return $this->ownsTicket($user, $ticket);
    }

    public function forceDelete(Authenticatable $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine if user can reply to a ticket.
     */
    public function reply(Authenticatable $user, Ticket $ticket): bool
    {
        return ($this->ownsTicket($user, $ticket) || $this->canAccessWorkspace($user, $ticket))
            && ! $ticket->isClosed();
    }

    /**
     * Check if user owns the ticket.
     */
    protected function ownsTicket(Authenticatable $user, Ticket $ticket): bool
    {
        return (string) $ticket->user_id === (string) $user->getAuthIdentifier();
    }

    /**
     * Check if user can access ticket through workspace membership.
     * Override this method to implement workspace-based authorization.
     */
    protected function canAccessWorkspace(Authenticatable $user, Ticket $ticket): bool
    {
        // If workspace support is not enabled, return false
        if (! $ticket->relationLoaded('workspace') && ! $ticket->workspace_id) {
            return false;
        }

        // Example implementation - uncomment and adjust for your needs:

        // If using bhhaskin/laravel-workspaces:
        // $workspace = $ticket->workspace;
        // if ($workspace && method_exists($workspace, 'hasUser')) {
        //     return $workspace->hasUser($user);
        // }

        // If using a custom membership check:
        // if ($ticket->workspace_id && method_exists($user, 'belongsToWorkspace')) {
        //     return $user->belongsToWorkspace($ticket->workspace_id);
        // }

        return false;
    }
}
