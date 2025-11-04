<?php

namespace Bhhaskin\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TicketAssociation extends Model
{
    protected $table = 'ticketables';

    protected $guarded = ['*'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ticketable(): MorphTo
    {
        return $this->morphTo();
    }
}
