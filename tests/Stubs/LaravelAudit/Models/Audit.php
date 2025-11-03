<?php

namespace LaravelAudit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Audit extends Model
{
    protected $table = 'audits';

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $audit): void {
            if (! $audit->uuid) {
                $audit->uuid = (string) Str::uuid();
            }
        });
    }
}
