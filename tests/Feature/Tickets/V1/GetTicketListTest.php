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
            * 1. Ticket List
            *  - It should be not able to get ticket list without access token
            *  - It should be able to get ticket list with valid access token
            * 2. Filter Ticket
            *  - It should be able to filter ticket list by `startDate`, `endDate`, `status` and `search`. 
     *  
     */ 

class GetTicketListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_get_ticket_list_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/tickets');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_get_ticket_list_with_valid_access_token(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        // Create some tickets to paginate
        Ticket::factory()->count(15)->create([
            'title' => 'Ticket Title',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => $user->id,
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/tickets');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message'=> 'Fetch ticket list successfully'
                
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_it_should_be_able_to_filter_ticket_list_by_start_date_end_date_status_and_search(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS');

        Ticket::factory()->create([
            'ticket_code' => 'TCK-001',
            'title' => 'Login issue target',
            'description' => 'Cannot login',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => $user->id,
            'created_at' => '2026-08-01 10:00:00'
        ]);

        Ticket::factory()->create([
            'ticket_code' => 'TCK-002',
            'title' => 'Dashboard error',
            'description' => '500 error on dashboard',
            'status' => Ticket::STATUS_RESOLVED,
            'user_id' => $user->id,
            'created_at' => '2026-08-15 10:00:00'
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/tickets?startDate=2026-08-01&endDate=2026-08-10&status=OPENED&search=target');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Fetch ticket list successfully',
                'meta' => [
                    'totalRecord' => 1,
                ]
            ])
            ->assertJsonFragment([
                'ticketCode' => 'TCK-001',
                'title' => 'Login issue target',
                'description' => 'Cannot login',
                'status' => Ticket::STATUS_OPENED,
                'createdBy' => [
                    'userId' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->role,
                ],
            ]);
            
        $responseData = $response->json('data');
        $this->assertCount(1, $responseData);
        $this->assertEquals('TCK-001', $responseData[0]['ticketCode']);
    }
}
