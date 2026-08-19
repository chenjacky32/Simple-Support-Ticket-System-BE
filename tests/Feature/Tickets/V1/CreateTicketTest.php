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
        * 1. CreateTicket 
        *  - It should be not able to create ticket without access token
        *  - It should be able to create ticket with valid access token
        * 2. Protected Role (USERS only)
        *  - It should be throw response 403 Forbidden when role is not USERS
     *  
     */ 

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_create_ticket_without_access_token(): void
    {
        $response = $this->postJson('/api/v1/tickets', [
            'title' => 'System down',
            'description' => 'The whole system is down',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_create_ticket_with_valid_access_token(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        $response = $this->withToken($token)
            ->postJson('/api/v1/tickets', [
            'title' => 'System down',
            'description' => 'The whole system is down',
        ]);

        $responseData = $response->json('data');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message'=> "New ticket created successfully",
            ])
            ->assertJsonFragment(
                [
                    'id'=> $responseData['id'],
                    'date'=> $responseData['date'],
                    'ticketCode'=> $responseData['ticketCode'],
                    'title'=> 'System down',
                    'description'=> 'The whole system is down',
                    'attachmentPath'=> $responseData['attachmentPath'],
                    'status' => Ticket::STATUS_OPENED,
                    'resolvedAt'=> null,
                    'createdBy'=> [
                        'userId'=> $user->id,
                        'name'=> $user->name,
                        'email'=> $user->email,
                        'role'=> $user->role->role,
                    ]
                ]
            );

        $this->assertDatabaseHas('tickets', [
            'title' => 'System down',
            'description' => 'The whole system is down',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_users(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('ADMIN');

        $response = $this->withToken($token)
            ->postJson('/api/v1/tickets', [
            'title' => 'System down',
            'description' => 'The whole system is down',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
