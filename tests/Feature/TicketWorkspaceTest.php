<?php

use Bhhaskin\LaravelWorkspaces\Models\Workspace;
use Bhhaskin\Tickets\Http\Resources\TicketResource;
use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Models\TicketReply;
use Bhhaskin\Tickets\Support\TicketWorkspace;
use Bhhaskin\Tickets\Tests\Fixtures\User;

beforeEach(function () {
    if (! TicketWorkspace::available()) {
        $this->markTestSkipped('Workspace integration not available.');
    }
});

test('ticket can be assigned to a workspace', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();

    $ticket = Ticket::factory()->for($user)->create();
    $ticket->assignWorkspace($workspace);

    expect($ticket->fresh()->workspace)->toBeInstanceOf(Workspace::class)
        ->and($ticket->workspace_id)->toBe($workspace->id);

    $scoped = Ticket::forWorkspace($workspace)->pluck('id')->all();

    expect($scoped)->toContain($ticket->id);
});

test('ticket resource includes workspace when loaded', function () {
    $workspace = Workspace::factory()->create(['name' => 'Anchor DNS']);
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create(['workspace_id' => $workspace->id]);
    TicketReply::factory()->forTicket($ticket)->for($user)->create();

    $ticket->load(['workspace', 'replies.user']);

    $payload = (new TicketResource($ticket))->toArray(request());

    expect($payload['workspace']['id'])->toBe($workspace->id)
        ->and($payload['workspace']['name'])->toBe('Anchor DNS');
});
