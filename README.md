# laravel-tickets

`bhhaskin/laravel-tickets` provides a reusable ticketing system for Laravel 10/11 applications with optional Markdown rendering, polymorphic attachments, audit logging, and workspace scoping.

## Installation

```bash
composer require bhhaskin/laravel-tickets:^0.1.0
```

Publish config and migrations if you need to customise them:

```bash
php artisan vendor:publish --tag=laravel-tickets-config
php artisan vendor:publish --tag=laravel-tickets-migrations
```

Run the package migrations:

```bash
php artisan migrate
```

## Features

- SQLite-friendly migrations and Testbench-ready factories.
- Markdown rendering for ticket bodies and replies (via `Str::markdown`).
- Optional audit trail if [`bhhaskin/laravel-audit`](https://github.com/bhhaskin/laravel-audit) is installed.
- Polymorphic associations (`ticketables`) to link any Eloquent model to a ticket.
- Optional workspace integration when [`bhhaskin/laravel-workspaces`](https://github.com/bhhaskin/laravel-workspaces) is present.

## Ticket Model

```php
use Bhhaskin\Tickets\Models\Ticket;

$ticket = Ticket::create([
    'user_id' => $user->id,
    'subject' => 'DNS outage',
    'body' => "We cannot reach example.com from the LA office.",
    'priority' => Ticket::PRIORITY_HIGH,
]);

// Attach arbitrary models
$ticket->attachModel($site);

// Optional: assign a workspace if available
if (class_exists(\Bhhaskin\LaravelWorkspaces\Support\WorkspaceConfig::class)) {
    $ticket->assignWorkspace($workspace);
}

// Replies render markdown automatically
$ticket->replies()->create([
    'user_id' => $user->id,
    'body' => "Here is **more** context",
]);
```

## Scopes

```php
// Tickets for a specific user
$mine = Ticket::forUser($user)->latest()->get();

// Tickets for a workspace (works when laravel-workspaces is installed)
$workspaceTickets = Ticket::forWorkspace($workspace)->with(['owner','replies','associations.ticketable'])->paginate();
```

## Optional Packages

- Install `bhhaskin/laravel-audit` to automatically log ticket and reply lifecycle events.
- Install `bhhaskin/laravel-workspaces` to scope tickets by workspace (adds `workspace()` relation and resource payload).

Both integrations are auto-detected—no additional configuration is required once the packages are installed.

## Testing

```bash
composer test
```

The suite uses Orchestra Testbench with in-memory SQLite and includes stubs for optional integrations so tests pass without extra dependencies.
