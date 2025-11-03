<?php

namespace Bhhaskin\Tickets\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin \Bhhaskin\Tickets\Models\TicketReply
 */
class TicketReplyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $ticket = null;

        if ($this->resource->relationLoaded('ticket')) {
            $ticket = $this->resource->getRelation('ticket');
        } else {
            $ticket = $this->ticket ?? null;
        }

        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'ticket_uuid' => $ticket?->uuid,
            'body' => $this->body,
            'body_html' => $this->body !== null ? Str::markdown($this->body, config('tickets.markdown.options', [])) : null,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'author' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->getAuthIdentifier(),
                    'name' => $this->user->name ?? null,
                    'email' => $this->user->email ?? null,
                ];
            }, [
                'id' => $this->user_id,
            ]),
        ];
    }
}
