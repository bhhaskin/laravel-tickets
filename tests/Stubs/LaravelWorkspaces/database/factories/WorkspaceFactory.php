<?php

namespace Bhhaskin\LaravelWorkspaces\database\factories;

use Bhhaskin\LaravelWorkspaces\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
