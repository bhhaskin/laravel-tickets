<?php

namespace Bhhaskin\Tickets\Models;

use Bhhaskin\Tickets\Database\Factories\TicketReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
    ];

    protected $guarded = [
        'id',
        'ticket_id',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::created(function (self $reply) {
            $reply->ticket?->forceFill([
                'last_replied_at' => $reply->created_at ?? now(),
            ])->save();
        });
    }

    protected static function newFactory(): TicketReplyFactory
    {
        return TicketReplyFactory::new();
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('tickets.user_model'));
    }
}
