<?php

namespace Tests\Feature\Users\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

 /**
     * Test Case Integration Testing
     *  
        * Users Group Routing Collection Testing    
            * 1. Update User Status 
            *  - It should be not able to update user status without access token
            *  - It should be able to update user status with valid access token
            * 2. Protected Role (SUPERADMIN only)
            *  - It should be throw response 403 Forbidden when role is not SUPERADMIN
     *  
     */ 

class UpdateUserStatusTest extends TestCase
{
    use RefreshDatabase;
    public function test_it_should_be_not_able_to_update_user_status_without_access_token(): void
    {
        $response = $this->patchJson('/api/v1/users/1/status');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_update_user_status_with_valid_access_token(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('SUPERADMIN', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_active' => false,
        ]);

        $response = $this->withToken($token)
            ->patchJson('/api/v1/users/'.$user->id.'/status', [
                'isActive' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'user status updated successfully',
            ])
            ->assertJsonFragment([
                'id'=> $user->id,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'SUPERADMIN',
                'isActive' => true,
            ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_superadmin(): void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_active' => false,
        ]);

        $response = $this->withToken($token)
            ->patchJson('/api/v1/users/'.$user->id.'/status', [
                'isActive' => true,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
