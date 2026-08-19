<?php

declare(strict_types=1);

namespace Tests\Feature\Tickets\V1;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
        * Tickets Group Routing Collection Testing    
            * 1. GetTicketDetail 
            *  - It should be not able to get ticket detail without access token
            *  - It should be able to get ticket detail with valid access token
     *  
     */ 

class GetTicketDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_get_ticket_detail_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/tickets/01HZZZZZZZZZZZZZZZZZZZZZZZ');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_get_ticket_detail_with_valid_access_token(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        $ticket = Ticket::forceCreate([
            'ticket_code' => 'TCK-001',
            'title' => 'Login issue',
            'description' => 'Cannot login',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/tickets/' . $ticket->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message'=> "Fetch ticket detail successfully",
            ])
            ->assertJsonFragment([
                'ticketCode' => 'TCK-001',
                'title' => 'Login issue',
                'description' => 'Cannot login',
                'status' => Ticket::STATUS_OPENED,
                'createdBy' => [
                    'userId' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->role,
                ],
            ])
            ->assertJsonPath('data.id', $ticket->id);
    }
}
