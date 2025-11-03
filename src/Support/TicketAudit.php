<?php

namespace Bhhaskin\Tickets\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TicketAudit
{
    public static function enabled(): bool
    {
        $loggerClass = 'LaravelAudit\\Logging\\AuditLogger';

        return class_exists($loggerClass) && App::bound($loggerClass);
    }

    public static function record(Model $model, string $event, array $meta = []): void
    {
        if (! self::enabled()) {
            return;
        }

        $loggerClass = 'LaravelAudit\\Logging\\AuditLogger';

        App::make($loggerClass)->record($model, $event, $meta);
    }
}
