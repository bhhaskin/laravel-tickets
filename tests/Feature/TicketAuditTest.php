<?php

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Models\TicketReply;
use Bhhaskin\Tickets\Support\TicketAudit;
use Bhhaskin\Tickets\Tests\Fixtures\Site;
use Bhhaskin\Tickets\Tests\Fixtures\Team;
use Bhhaskin\Tickets\Tests\Fixtures\User;
use LaravelAudit\Models\Audit;

beforeEach(function () {
    if (! TicketAudit::enabled()) {
        $this->markTestSkipped('Audit package not installed.');
    }

    config(['audit.enabled' => true]);

    Audit::query()->delete();
});

test('ticket lifecycle events are audited', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create([
        'subject' => 'DNS outage',
    ]);

    // Update a fillable field (subject) to trigger the updated event
    $ticket->update(['subject' => 'DNS outage - Updated']);
    $ticket->delete();

    $audits = Audit::query()
        ->where('auditable_type', $ticket->getMorphClass())
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($audits)->toEqualCanonicalizing([
        'created',
        'updated',
        'deleted',
    ]);
});

test('ticket replies and attachments write audit records', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();
    $reply = TicketReply::factory()->for($ticket)->for($user)->create();

    $reply->update(['body' => 'Updated context']);

    $team = Team::factory()->create();
    $site = Site::factory()->create();

    $ticket->attachModel($team, $user);
    $ticket->attachModel($site, $user);
    $ticket->detachModel($team, $user);

    $events = Audit::query()->pluck('event');

    expect($events->contains('created'))->toBeTrue()
        ->and($events->contains('updated'))->toBeTrue();

    $actions = Audit::query()
        ->where('event', 'updated')
        ->get()
        ->map(fn (Audit $audit) => $audit->meta['action'] ?? null)
        ->filter()
        ->values();

    expect($actions)->toContain('attached')
        ->and($actions)->toContain('detached');
});
