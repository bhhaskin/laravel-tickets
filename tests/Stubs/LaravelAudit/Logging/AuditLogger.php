<?php

namespace LaravelAudit\Logging;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use LaravelAudit\Models\Audit;

class AuditLogger
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function record(Model $model, string $event, array $meta = []): void
    {
        if (! ($this->config['enabled'] ?? false)) {
            return;
        }

        if (! Arr::get($this->config, "events.{$event}", true)) {
            return;
        }

        [$old, $new] = $this->resolveValues($model, $event);

        $payload = [
            'event' => $event,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'meta' => $meta === [] ? null : $meta,
        ];

        if ($user = $this->resolveUser()) {
            $payload['user_type'] = $user->getMorphClass();
            $payload['user_id'] = $user->getAuthIdentifier();
        }

        Audit::query()->create($payload);
    }

    protected function resolveUser(): ?Authenticatable
    {
        if (function_exists('auth') && auth()->check()) {
            return auth()->user();
        }

        return null;
    }

    /**
     * @return array{0: array<string, mixed>|null, 1: array<string, mixed>|null}
     */
    protected function resolveValues(Model $model, string $event): array
    {
        $ignore = (array) Arr::get($this->config, 'ignore', []);

        $filter = static fn (?array $values): ?array => $values !== null
            ? Arr::except($values, $ignore)
            : null;

        return match ($event) {
            'created' => [null, $filter($model->getAttributes())],
            'deleted' => [$filter($model->getOriginal()), null],
            default => [$filter($model->getOriginal()), $filter($model->getAttributes())],
        };
    }
}
