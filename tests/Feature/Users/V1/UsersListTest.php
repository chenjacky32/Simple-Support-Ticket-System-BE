<?php

declare(strict_types=1);

namespace Tests\Feature\Users\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
        * Users Group Routing Collection Testing    
            * 1. Users List
            *  - It should be not able to access users list without access token
            *  - It should be able to access users list with valid access token
            * 2. Filter Users
            *  - It should be able to filter users list by search and status 
            * 3. Protected Role (SUPERADMIN only)
            *  - It should be throw response 403 Forbidden when role is not SUPERADMIN
     *  
     */ 

class UsersListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_access_users_list_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/users/list');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_access_users_list_with_valid_access_token(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('SUPERADMIN', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create some users to paginate
        User::factory()->count(15)->create([
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/users/list?page=1&size=10');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'ok',
                'meta' => [
                    'page' => 1,
                    'size' => 10,
                    'totalRecord' => 16, // 15 generated + 1 admin user
                    'totalPage' => 2,
                    'hasPrev' => false,
                    'hasNext' => true,
                ],
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_it_should_be_able_to_filter_users_list_by_search_and_status(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('SUPERADMIN', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Active match
        User::factory()->create([
            'name' => 'Target Search Active',
            'email' => 'target.active@example.com',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        // Inactive match
        User::factory()->create([
            'name' => 'Target Search Inactive',
            'email' => 'target.inactive@example.com',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => false,
        ]);

        // Active non-match
        User::factory()->create([
            'name' => 'Other Active',
            'email' => 'other.active@example.com',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        // Query status ACTIVE and search "Target"
        $response = $this->withToken($token)
            ->getJson('/api/v1/users/list?status=ACTIVE&search=Target');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'meta' => [
                    'totalRecord' => 1,
                ],
            ])
            ->assertJsonFragment([
                'name' => 'Target Search Active',
                'email' => 'target.active@example.com',
            ])
            ->assertJsonMissing([
                'name' => 'Target Search Inactive',
            ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_superadmin(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS', [
            'name' => 'User',
            'email' => 'user@example.com',
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/users/list');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
