<?php

namespace Bhhaskin\LaravelWorkspaces\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Bhhaskin\LaravelWorkspaces\database\factories\WorkspaceFactory::new();
    }
}
