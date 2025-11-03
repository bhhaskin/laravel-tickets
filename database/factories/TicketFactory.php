<?php

namespace Bhhaskin\Tickets\Database\Factories;

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Support\TicketsUserResolver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $userModel = TicketsUserResolver::resolveModel();

        return [
            'user_id' => $userModel::factory(),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'status' => Ticket::STATUS_NEW,
            'priority' => Ticket::PRIORITY_NORMAL,
            'closed_at' => null,
            'last_replied_at' => null,
        ];
    }

    public function inProgress(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ticket::STATUS_RESOLVED,
        ]);
    }

    public function closed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ticket::STATUS_CLOSED,
            'closed_at' => Carbon::now(),
        ]);
    }

    public function highPriority(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Ticket::PRIORITY_HIGH,
        ]);
    }

    public function lowPriority(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Ticket::PRIORITY_LOW,
        ]);
    }
}
