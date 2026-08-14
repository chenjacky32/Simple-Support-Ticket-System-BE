<?php

namespace Tests\Feature\Users\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
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

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['role' => 'USERS']);
    }
    
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
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/users/profile');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Fetch profile successfully',
            ])
            ->assertJsonFragment([
                'name' => "Admin User",
                'email' => "admin@example.com",
                'role' => "USERS",
                'isActive' => true,
            ]);
    }
}
