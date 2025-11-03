<?php

namespace Bhhaskin\Tickets;

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Policies\TicketPolicy;
use Bhhaskin\Tickets\Support\TicketAudit;
use Bhhaskin\Tickets\Models\TicketReply;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TicketsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tickets.php', 'tickets');
    }

    public function boot(): void
    {
        $this->configureUserModel();
        $this->configureWorkspaceModel();
        $this->registerPublishing();
        $this->registerMigrations();
        $this->registerPolicies();
        $this->registerAuditHooks();
    }

    protected function configureUserModel(): void
    {
        if (! config('tickets.user_model')) {
            config()->set('tickets.user_model', config('auth.providers.users.model'));
        }
    }

    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/tickets.php' => config_path('tickets.php'),
        ], 'laravel-tickets-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'laravel-tickets-migrations');
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
    }

    protected function configureWorkspaceModel(): void
    {
        if (config('tickets.workspace_model')) {
            return;
        }

        if (class_exists('Bhhaskin\\LaravelWorkspaces\\Support\\WorkspaceConfig')) {
            config()->set('tickets.workspace_model', \Bhhaskin\LaravelWorkspaces\Support\WorkspaceConfig::workspaceModel());
        }
    }

    protected function registerAuditHooks(): void
    {
        if (! TicketAudit::enabled()) {
            return;
        }

        Ticket::created(fn (Ticket $ticket) => TicketAudit::record($ticket, 'created'));
        Ticket::updated(function (Ticket $ticket): void {
            if ($ticket->wasChanged()) {
                TicketAudit::record($ticket, 'updated');
            }
        });
        Ticket::deleted(fn (Ticket $ticket) => TicketAudit::record($ticket, 'deleted'));

        TicketReply::created(fn (TicketReply $reply) => TicketAudit::record($reply, 'created'));
        TicketReply::updated(function (TicketReply $reply): void {
            if ($reply->wasChanged()) {
                TicketAudit::record($reply, 'updated');
            }
        });
        TicketReply::deleted(fn (TicketReply $reply) => TicketAudit::record($reply, 'deleted'));
    }
}
