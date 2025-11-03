<?php

namespace Bhhaskin\Tickets\Tests\Fixtures;

use Bhhaskin\Tickets\Concerns\HasTickets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    use HasTickets;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Bhhaskin\Tickets\Tests\Database\Factories\TeamFactory::new();
    }
}
