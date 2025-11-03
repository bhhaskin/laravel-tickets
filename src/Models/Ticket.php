<?php

namespace Bhhaskin\Tickets\Models;

use Bhhaskin\Tickets\Database\Factories\TicketFactory;
use Bhhaskin\Tickets\Models\TicketAssociation;
use Bhhaskin\Tickets\Support\TicketAudit;
use Bhhaskin\Tickets\Support\TicketWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $subject
 * @property string $body
 * @property string $status
 * @property string $priority
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon|null $last_replied_at
 */
class Ticket extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'uuid',
        'status',
        'priority',
        'closed_at',
        'last_replied_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'last_replied_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_NEW,
        'priority' => self::PRIORITY_NORMAL,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = (string) Str::uuid();
            }
        });
    }

    protected static function newFactory(): TicketFactory
    {
        return TicketFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('tickets.user_model'));
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function related(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'ticketable', 'ticketables', 'ticket_id', 'ticketable_id')
            ->withTimestamps();
    }

    public function associations(): HasMany
    {
        return $this->hasMany(TicketAssociation::class)->with('ticketable');
    }

    public function workspace(): BelongsTo
    {
        return TicketWorkspace::relation($this);
    }

    public function attachModel(EloquentModel $model): void
    {
        $result = $this->related($model::class)->syncWithoutDetaching([$model->getKey()]);

        if (! empty($result['attached'])) {
            TicketAudit::record($this, 'updated', [
                'action' => 'attached',
                'related_type' => $model::class,
                'related_id' => $model->getKey(),
            ]);
        }
    }

    public function detachModel(EloquentModel $model): void
    {
        $detached = $this->related($model::class)->detach($model->getKey());

        if ($detached > 0) {
            TicketAudit::record($this, 'updated', [
                'action' => 'detached',
                'related_type' => $model::class,
                'related_id' => $model->getKey(),
            ]);
        }
    }

    public function assignWorkspace(?EloquentModel $workspace): void
    {
        if ($workspace !== null) {
            TicketWorkspace::guardModel($workspace);
        }

        $this->workspace()->associate($workspace);
        $this->save();
    }

    public function scopeForUser(Builder $query, $user): Builder
    {
        $userId = is_object($user) && method_exists($user, 'getAuthIdentifier')
            ? $user->getAuthIdentifier()
            : $user;

        return $query->where('user_id', $userId);
    }

    public function scopeForWorkspace(Builder $query, EloquentModel $workspace): Builder
    {
        TicketWorkspace::guardModel($workspace);

        return $query->where('workspace_id', $workspace->getKey());
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function close(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ])->save();
    }

    public function reopen(): void
    {
        $this->forceFill([
            'status' => self::STATUS_IN_PROGRESS,
            'closed_at' => null,
        ])->save();
    }
}
