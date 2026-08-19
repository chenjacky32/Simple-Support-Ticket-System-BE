<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_code' => 'TCK-' . fake()->unique()->numerify('####'),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'attachment_path' => null,
            'status' => Ticket::STATUS_OPENED,
            'user_id' => User::factory(),
            'resolved_at' => null,
        ];
    }
}
