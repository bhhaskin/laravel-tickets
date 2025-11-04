<?php

namespace Bhhaskin\Tickets\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;

/**
 * @mixin \Bhhaskin\Tickets\Models\Ticket
 */
class TicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->id,
            'uuid' => $this->uuid,
            'ticket_uuid' => $this->uuid,
            'subject' => $this->subject,
            'body' => $this->body,
            'body_html' => $this->body !== null ? Str::markdown($this->body, config('tickets.markdown.options', [])) : null,
            'status' => $this->status,
            'priority' => $this->priority,
            'closed_at' => optional($this->closed_at)?->toIso8601String(),
            'last_replied_at' => optional($this->last_replied_at)?->toIso8601String(),
            'reply_count' => isset($this->replies_count)
                ? $this->replies_count
                : ($this->relationLoaded('replies') ? $this->replies->count() : 0),
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'owner' => $this->whenLoaded('user', function () use ($request) {
                $canViewEmail = $request->user()
                    && ((string) $request->user()->getAuthIdentifier() === (string) $this->user_id
                        || $request->user()->can('viewAny', $this->resource));

                return [
                    'id' => $this->user->getAuthIdentifier(),
                    'name' => $this->user->name ?? null,
                    'email' => $canViewEmail ? ($this->user->email ?? null) : null,
                ];
            }, [
                'id' => $this->user_id,
            ]),
            'workspace' => $this->whenLoaded('workspace', function () {
                return [
                    'id' => $this->workspace->getKey(),
                    'name' => $this->workspace->name ?? null,
                    'slug' => $this->workspace->slug ?? null,
                ];
            }),
            'replies' => $this->formatReplies($request),
            'related' => $this->formatRelated($request),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function formatReplies(Request $request): array
    {
        $replies = $this->whenLoaded('replies');

        if ($replies instanceof MissingValue) {
            return [];
        }

        return TicketReplyResource::collection($replies)->toArray($request);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function formatRelated(Request $request): array
    {
        $associations = $this->whenLoaded('associations');

        if ($associations instanceof MissingValue) {
            return [];
        }

        return TicketAssociationResource::collection($associations)->toArray($request);
    }
}
