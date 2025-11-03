<?php

namespace Bhhaskin\Tickets\Concerns;

use Bhhaskin\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTickets
{
    public function tickets(): MorphToMany
    {
        return $this->morphToMany(Ticket::class, 'ticketable', 'ticketables')
            ->withTimestamps();
    }
}
