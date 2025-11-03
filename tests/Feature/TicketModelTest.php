<?php

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Models\TicketReply;
use Bhhaskin\Tickets\Tests\Fixtures\User;
use Illuminate\Support\Carbon;

test('ticket generates uuid and defaults', function () {
    $user = User::factory()->create();

    $ticket = Ticket::factory()->for($user)->create();

    expect($ticket->uuid)->not->toBeNull()
        ->and($ticket->status)->toBe(Ticket::STATUS_NEW)
        ->and($ticket->priority)->toBe(Ticket::PRIORITY_NORMAL);
});

test('scope for user returns only owned tickets', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $owned = Ticket::factory()->count(2)->for($user)->create();
    Ticket::factory()->for($other)->create();

    $results = Ticket::forUser($user)->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('id')->sort()->values())->toEqual($owned->pluck('id')->sort()->values());
});

test('ticket reply updates last replied timestamp', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create([
        'last_replied_at' => null,
    ]);

    $replyTime = Carbon::now()->subHour();

    TicketReply::factory()
        ->forTicket($ticket)
        ->for($user)
        ->create([
            'created_at' => $replyTime,
        ]);

    $ticket->refresh();

    expect($ticket->last_replied_at)->not->toBeNull()
        ->and($ticket->last_replied_at->isSameSecond($replyTime))->toBeTrue();
});

test('ticket can be closed and reopened', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $ticket->close();

    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_CLOSED)
        ->and($ticket->fresh()->closed_at)->not->toBeNull();

    $ticket->reopen();

    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_IN_PROGRESS)
        ->and($ticket->fresh()->closed_at)->toBeNull();
});
