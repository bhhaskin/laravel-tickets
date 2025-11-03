<?php

namespace Bhhaskin\Tickets\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketWorkspace
{
    public static function available(): bool
    {
        return static::modelClass(false) !== null;
    }

    /**
     * @return class-string<Model>|null
     */
    public static function modelClass(bool $throw = true): ?string
    {
        $configured = config('tickets.workspace_model');

        if (is_string($configured) && class_exists($configured)) {
            return $configured;
        }

        if (class_exists('Bhhaskin\\LaravelWorkspaces\\Support\\WorkspaceConfig')) {
            $workspaceModel = \Bhhaskin\LaravelWorkspaces\Support\WorkspaceConfig::workspaceModel();
            config()->set('tickets.workspace_model', $workspaceModel);

            return $workspaceModel;
        }

        if ($throw) {
            throw new \RuntimeException('Workspace integration is not enabled.');
        }

        return null;
    }

    public static function guardModel(Model $workspace): void
    {
        $expected = static::modelClass();

        if (! $workspace instanceof $expected) {
            throw new \InvalidArgumentException(sprintf(
                'Expected workspace instance of %s; received %s.',
                $expected,
                $workspace::class,
            ));
        }
    }

    public static function relation(Model $ticket): BelongsTo
    {
        $model = static::modelClass();

        return $ticket->belongsTo($model, 'workspace_id');
    }
}
