<?php

namespace LaravelAudit;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use LaravelAudit\Logging\AuditLogger;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(AuditLogger::class, function (Container $app): AuditLogger {
            $config = array_merge([
                'enabled' => true,
                'events' => [
                    'created' => true,
                    'updated' => true,
                    'deleted' => true,
                ],
                'ignore' => ['updated_at'],
            ], $app['config']->get('audit', []));

            $app['config']->set('audit', $config);

            return new AuditLogger($config);
        });
    }
}
