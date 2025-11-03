<?php

use Bhhaskin\Tickets\Http\Resources\TicketReplyResource;
use Bhhaskin\Tickets\Http\Resources\TicketResource;
use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Models\TicketReply;
use Bhhaskin\Tickets\Tests\Fixtures\User;

function resourceRequest()
{
    return request();
}

test('ticket resource renders markdown and owner data', function () {
    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $ticket = Ticket::factory()->for($user)->create([
        'body' => "Help **please**",
    ]);

    $reply = TicketReply::factory()->forTicket($ticket)->for($user)->create([
        'body' => "Here is _more_ info",
    ]);

    $ticket->load(['user', 'replies.user'])->loadCount('replies');

    $data = (new TicketResource($ticket))->toArray(resourceRequest());

    expect($data['body_html'])->toContain('<strong>please</strong>')
        ->and($data['owner']['name'])->toBe('Jane Doe')
        ->and($data['reply_count'])->toBe(1)
        ->and($data['replies'])->toHaveCount(1)
        ->and($data['replies'][0]['body_html'])->toContain('<em>more</em>')
        ->and($data['ticket_id'])->toBe($ticket->id)
        ->and($data['ticket_uuid'])->toBe($ticket->uuid)
        ->and($data['replies'][0]['ticket_uuid'])->toBe($ticket->uuid);

    $replyData = (new TicketReplyResource($reply->setRelation('ticket', $ticket)->setRelation('user', $user)))->toArray(resourceRequest());

    expect($replyData['ticket_uuid'])->toBe($ticket->uuid)
        ->and($replyData['body_html'])->toContain('<em>more</em>')
        ->and($replyData['author']['id'])->toBe($user->id);
});
