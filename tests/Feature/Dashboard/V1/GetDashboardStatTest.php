<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard\V1;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
         * Dashboard Group Routing Collection Testing    
            * 1. Get Dashboard Stats
            *  - It should be not able to get dashboard stat without access token
            *  - It should be able to get dashboard stat with valid access token
            * 2. Filter Dashboard
            *  - It should be able to filter dashboard stat by `startDate`, `endDate`.      
     *  
     */ 

class GetDashboardStatTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_get_dashboard_stat_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/dashboard/stat');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_get_dashboard_stat_with_valid_access_token(): void
    {
        [$admin, $token] = $this->createAndAuthenticateUser('ADMIN');

        $response = $this->withToken($token)
            ->getJson('/api/v1/dashboard/stat');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message'=> 'Fetch dashboard stats successfully',
            ])
            ->assertJsonFragment(
                [
                    'startDate'=> null,
                    'endDate'=> null,
                    'totalTickets'=> 0,
                    'openedTickets' => 0,
                    'inprogressTickets'=> 0,
                    'resolvedTickets'=> 0,
                    'statusCompositions'=> [
                        [
                            'status'=> 'OPENED',
                            'percentage'=> 0,
                            'count'=> 0
                        ],
                        [
                            'status'=> 'INPROGRESS',
                            'percentage'=> 0,
                            'count'=> 0
                        ],
                        [
                            'status'=> 'RESOLVED',
                            'percentage'=> 0,
                            'count'=> 0
                        ]
                    ]
                ]
            );
    }

    public function test_it_should_be_able_to_filter_dashboard_stat_by_start_date_and_end_date(): void
    {
        [$admin, $token] = $this->createAndAuthenticateUser('ADMIN');

        // Create some tickets inside the date range and outside
        $user = User::factory()->create(['role_id' => Role::where('role', 'USERS')->first()->id]);

        Ticket::forceCreate([
            'ticket_code' => 'TCK-001',
            'title' => 'In range ticket',
            'description' => 'Inside range',
            'status' => Ticket::STATUS_OPENED,
            'user_id' => $user->id,
            'created_at' => '2026-08-05 10:00:00'
        ]);

        Ticket::forceCreate([
            'ticket_code' => 'TCK-002',
            'title' => 'Out of range ticket',
            'description' => 'Outside range',
            'status' => Ticket::STATUS_RESOLVED,
            'user_id' => $user->id,
            'created_at' => '2026-08-20 10:00:00'
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/dashboard/stat?startDate=2026-08-01&endDate=2026-08-10');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message'=> 'Fetch dashboard stats successfully',
            ])
            ->assertJsonFragment(
                [
                    'startDate'=> '2026-08-01',
                    'endDate'=> '2026-08-10',
                    'totalTickets'=> 1,
                    'openedTickets' => 1,
                    'inprogressTickets'=> 0,
                    'resolvedTickets'=> 0,
                    'statusCompositions'=> [
                        [
                            'status'=> 'OPENED',
                            'percentage'=> 100,
                            'count'=> 1
                        ],
                        [
                            'status'=> 'INPROGRESS',
                            'percentage'=> 0,
                            'count'=> 0
                        ],
                        [
                            'status'=> 'RESOLVED',
                            'percentage'=> 0,
                            'count'=> 0
                        ]
                    ]
                ]
            );
    }
}
