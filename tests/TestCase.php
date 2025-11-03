<?php

namespace Bhhaskin\Tickets\Tests;

use Bhhaskin\Tickets\TicketsServiceProvider;
use Bhhaskin\Tickets\Tests\Fixtures\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        $providers = [];

        if (class_exists(\LaravelAudit\AuditServiceProvider::class)) {
            $providers[] = \LaravelAudit\AuditServiceProvider::class;
        }

        $providers[] = TicketsServiceProvider::class;

        return $providers;
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set('auth.defaults.guard', 'web');
        $app['config']->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);
        $app['config']->set('tickets.user_model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $workspaceMigrations = __DIR__.'/Stubs/LaravelWorkspaces/database/migrations';

        if (is_dir($workspaceMigrations)) {
            $this->loadMigrationsFrom($workspaceMigrations);
        }

        $auditMigrations = __DIR__.'/Stubs/LaravelAudit/database/migrations';

        if (is_dir($auditMigrations)) {
            $this->loadMigrationsFrom($auditMigrations);
        }

        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }
}
