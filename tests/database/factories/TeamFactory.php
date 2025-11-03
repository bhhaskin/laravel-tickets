<?php

namespace Bhhaskin\Tickets\Tests\Database\Factories;

use Bhhaskin\Tickets\Tests\Fixtures\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
