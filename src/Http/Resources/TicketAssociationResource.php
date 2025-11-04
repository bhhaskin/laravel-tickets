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
                // Only expose basic information to prevent data leaks
                // If the model has a toResource method or is a JsonResource, use it
                // Otherwise, only expose the ID and type
                if ($this->ticketable instanceof \Illuminate\Http\Resources\Json\JsonResource) {
                    return $this->ticketable;
                }

                if (method_exists($this->ticketable, 'toResource')) {
                    return $this->ticketable->toResource();
                }

                // Fallback: only expose minimal safe data
                return [
                    'id' => $this->ticketable->getKey(),
                    'type' => $this->ticketable_type,
                ];
            }),
        ];
    }
}
