<?php

namespace Bhhaskin\Tickets\Tests\Database\Factories;

use Bhhaskin\Tickets\Tests\Fixtures\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'domain' => $this->faker->domainName(),
        ];
    }
}
