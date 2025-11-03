<?php

namespace Bhhaskin\Tickets\Database\Factories;

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Models\TicketReply;
use Bhhaskin\Tickets\Support\TicketsUserResolver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketReply>
 */
class TicketReplyFactory extends Factory
{
    protected $model = TicketReply::class;

    public function definition(): array
    {
        $userModel = TicketsUserResolver::resolveModel();

        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => $userModel::factory(),
            'body' => $this->faker->paragraph(),
        ];
    }

    public function forTicket(Ticket $ticket): self
    {
        return $this->state(fn () => [
            'ticket_id' => $ticket->id,
        ]);
    }
}
