<?php

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Tests\Fixtures\User;
use Illuminate\Support\Facades\Gate;

test('user can view and update owned ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    expect(Gate::forUser($user)->allows('view', $ticket))->toBeTrue()
        ->and(Gate::forUser($user)->allows('update', $ticket))->toBeTrue();
});

test('user cannot view ticket they do not own', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $ticket = Ticket::factory()->for($other)->create();

    expect(Gate::forUser($user)->allows('view', $ticket))->toBeFalse();
});

test('user cannot reply to closed ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->closed()->create();

    expect(Gate::forUser($user)->allows('reply', $ticket))->toBeFalse();
});

test('user can reply to active ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->inProgress()->create();

    expect(Gate::forUser($user)->allows('reply', $ticket))->toBeTrue();
});
