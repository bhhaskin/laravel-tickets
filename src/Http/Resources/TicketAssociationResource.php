<?php

namespace Bhhaskin\Tickets\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Bhhaskin\Tickets\Models\TicketAssociation
 */
class TicketAssociationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ticketable_id,
            'type' => $this->ticketable_type,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'ticket' => [
                'id' => $this->ticket_id,
            ],
            'model' => $this->when($this->relationLoaded('ticketable') && $this->ticketable, function () {
                return method_exists($this->ticketable, 'toArray')
                    ? $this->ticketable->toArray()
                    : (array) $this->ticketable;
            }),
        ];
    }
}
