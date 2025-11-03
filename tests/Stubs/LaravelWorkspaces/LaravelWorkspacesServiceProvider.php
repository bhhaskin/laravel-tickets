<?php

namespace Bhhaskin\LaravelWorkspaces;

use Illuminate\Support\ServiceProvider;

class LaravelWorkspacesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/workspaces.php', 'workspaces');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
