<?php

namespace Bhhaskin\LaravelWorkspaces\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class WorkspaceConfig
{
    /**
     * @return class-string<Model>
     */
    public static function workspaceModel(): string
    {
        $model = config('workspaces.workspace_model', \Bhhaskin\LaravelWorkspaces\Models\Workspace::class);

        return static::ensureModel($model);
    }

    public static function invitationModel(): string
    {
        $model = config('workspaces.invitation_model');

        return static::ensureModel($model ?? \Bhhaskin\LaravelWorkspaces\Models\WorkspaceInvitation::class);
    }

    public static function userModel(): string
    {
        $model = config('workspaces.user_model')
            ?? config('auth.providers.users.model');

        return static::ensureModel($model ?? \Bhhaskin\Tickets\Tests\Fixtures\User::class);
    }

    /**
     * @param class-string<Model>|null $model
     * @return class-string<Model>
     */
    protected static function ensureModel(?string $model): string
    {
        if (is_string($model) && class_exists($model) && is_subclass_of($model, Model::class)) {
            return $model;
        }

        throw new InvalidArgumentException('Workspace configuration must point to a valid model class.');
    }
}
