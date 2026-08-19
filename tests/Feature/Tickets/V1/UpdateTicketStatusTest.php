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
            * 1. Update Ticket Status
            *  - It should be not able to update ticket status without access token
            *  - It should be able to update ticket status with valid access token
            * 2. Protected Role (ADMIN only)
            *  - It should be throw response 403 Forbidden when role is not ADMIN
            * 3. Invalid Status Validation
            *  - It should be not able to update ticket status with invalid status
     *  
     */ 

class UpdateTicketStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_update_ticket_status_without_access_token(): void
    {
        $response = $this->patchJson('/api/v1/tickets/01HZZZZZZZZZZZZZZZZZZZZZZZ/status', [
            'status' => Ticket::STATUS_RESOLVED,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_update_ticket_status_with_valid_access_token(): void
    {
        [$admin, $token] = $this->createAndAuthenticateUser('ADMIN');

        // Target ticket
        $ticket = Ticket::forceCreate([
            'ticket_code' => 'TCK-001',
            'title' => 'Login issue',
            'description' => 'Cannot login',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => User::factory()->create(['role_id' => Role::where('role', 'USERS')->first()->id])->id,
            'created_at' => now(),
        ]);

        $response = $this->withToken($token)
            ->patchJson("/api/v1/tickets/{$ticket->id}/status", [
                'status' => Ticket::STATUS_RESOLVED,
            ]);

        $responseData = $response->json('data');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Ticket status updated successfully',
            ])
            ->assertJsonFragment([
                'id'=> $responseData['id'],
                'status' => Ticket::STATUS_RESOLVED,
                'updatedAt' => $responseData['updatedAt'],
                'updatedBy'=> [
                    'userId' => $responseData['updatedBy']['userId'],
                    'name' => $responseData['updatedBy']['name'],
                    'email' => $responseData['updatedBy']['email'],
                    'role' => $responseData['updatedBy']['role'],
                ]
            ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_RESOLVED,
        ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_admin(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        $response = $this->withToken($token)
            ->patchJson('/api/v1/tickets/01HZZZZZZZZZZZZZZZZZZZZZZZ/status', [
                'status' => Ticket::STATUS_RESOLVED,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }

    public function test_it_should_be_not_able_to_update_ticket_status_with_invalid_status(): void
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
            ->patchJson("/api/v1/tickets/{$ticket->id}/status", [
                'status' => 'SUCCESS',
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'status'
                ]
            ]);
    }
}
