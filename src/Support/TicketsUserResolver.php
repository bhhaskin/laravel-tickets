<?php

namespace Bhhaskin\Tickets\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class TicketsUserResolver
{
    /**
     * @return class-string<Model&Authenticatable>
     */
    public static function resolveModel(): string
    {
        $model = config('tickets.user_model') ?? config('auth.providers.users.model');

        if (! is_string($model) || $model === '') {
            throw new \RuntimeException('Unable to determine the tickets user model. Set the tickets.user_model configuration value.');
        }

        return $model;
    }
}
