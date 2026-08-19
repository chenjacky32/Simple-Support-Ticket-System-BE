<?php

namespace Tests\Feature\Users\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
        * Users Group Routing Collection Testing    
            * 1. User Profile 
            *  - It should be not able to access user profile without access token
            *  - It should be able to access user profile with valid access token
     *  
     */ 

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_not_able_to_access_user_profile_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/users/profile');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_access_user_profile_with_valid_access_token():void
    {
        [$user, $token] = $this->createAndAuthenticateUser('USERS', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/users/profile');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Fetch profile successfully',
            ])
            ->assertJsonFragment([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'USERS',
                'isActive' => true,
            ]);
    }
}
