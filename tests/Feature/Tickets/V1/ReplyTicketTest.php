<?php

declare(strict_types=1);

namespace Tests\Feature\Tickets\V1;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
        * Tickets Group Routing Collection Testing    
            * 1. Reply Ticket 
            *  - It should be not able to reply ticket without access token
            *  - It should be able to reply ticket with valid access token
            * 2. Protected Role (ADMIN only)
            *  - It should be throw response 403 Forbidden when role is not ADMIN
     *  
     */ 

class ReplyTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_reply_ticket_without_access_token(): void
    {
        $response = $this->postJson('/api/v1/tickets/01HZZZZZZZZZZZZZZZZZZZZZZZ/replies', [
            'message' => 'This is a reply',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_reply_ticket_with_valid_access_token(): void
    {
        [$admin, $token] = $this->createAndAuthenticateUser('ADMIN');

        // Target ticket
        $ticket = Ticket::factory()->create([
            'ticket_code' => 'TCK-001',
            'title' => 'Login issue',
            'description' => 'Cannot login',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => User::factory()->create(['role_id' => Role::where('role', 'USERS')->first()->id])->id,
            'created_at' => now(),
        ]);

        $response = $this->withToken($token)
            ->postJson("/api/v1/tickets/{$ticket->id}/replies", [
                'message' => 'We are working on this.',
            ]);
        
        $responseData = $response->json('data');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Reply added successfully',
            ])
            ->assertJsonFragment([
                'id' => $responseData['id'],
                'ticketId' => $responseData['ticketId'],
                'message' => 'We are working on this.',
                'createdAt' => $responseData['createdAt'],
                'createdBy' => [
                    'userId' => $responseData['createdBy']['userId'],
                    'name' => $responseData['createdBy']['name'],
                    'email' => $responseData['createdBy']['email'],
                    'role' => $responseData['createdBy']['role'],
                ]
            ]);

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $ticket->id,
            'responded_by' => $admin->id,
            'message' => 'We are working on this.',
        ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_admin(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        $response = $this->withToken($token)
            ->postJson('/api/v1/tickets/01HZZZZZZZZZZZZZZZZZZZZZZZ/replies', [
                'message' => 'This is a reply',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
