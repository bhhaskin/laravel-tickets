<?php

use Bhhaskin\Tickets\Http\Resources\TicketResource;
use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Tests\Fixtures\Site;
use Bhhaskin\Tickets\Tests\Fixtures\Team;
use Bhhaskin\Tickets\Tests\Fixtures\User;

function ticketResourceRequest()
{
    return request();
}

test('tickets can attach arbitrary models', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();
    $team = Team::factory()->create();
    $site = Site::factory()->create();

    $ticket->attachModel($team, $user);
    $ticket->attachModel($site, $user);
    $ticket->attachModel($team, $user); // attaching again should not duplicate

    $ticket->load(['associations.ticketable']);

    expect($ticket->associations)->toHaveCount(2)
        ->and($ticket->related(Team::class)->first()?->id)->toBe($team->id)
        ->and($ticket->related(Site::class)->first()?->id)->toBe($site->id);

    expect($team->tickets)->toHaveCount(1)
        ->and($site->tickets)->toHaveCount(1);

    $ticket->detachModel($team, $user);
    $ticket->load('associations.ticketable');

    expect($ticket->associations)->toHaveCount(1)
        ->and($ticket->related(Team::class)->count())->toBe(0);

    $ticket->attachModel($team, $user);

    $ticket->load(['user', 'replies', 'associations.ticketable']);
    $resource = (new TicketResource($ticket))->toArray(ticketResourceRequest());

    expect($resource['related'])->toHaveCount(2)
        ->and(collect($resource['related'])->pluck('type'))->toContain(Team::class)
        ->and(collect($resource['related'])->pluck('type'))->toContain(Site::class);
});
