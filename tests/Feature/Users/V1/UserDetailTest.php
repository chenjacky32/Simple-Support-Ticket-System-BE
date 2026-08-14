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
            * 1. User Detail 
            *  - It should be not able to access user detail without access token
            *  - It should be able to access user detail with valid access token
            * 2. Protected Role (SUPERADMIN only)
            *  - It should be throw response 403 Forbidden when role is not SUPERADMIN
     *  
     */ 

class UserDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'SUPERADMIN']);
    }
    
    public function test_it_should_be_not_able_to_access_user_detail_without_access_token(): void
    {
        $response = $this->getJson('/api/v1/users/'. '123e4567');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_access_user_detail_with_valid_access_token(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'SUPERADMIN')->first()->id,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Fetch user details successfully',
            ])
            ->assertJsonFragment([
                'id'  => $user->id,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'SUPERADMIN',
                'isActive' => true,
            ]);
    }

    public function test_it_should_be_throw_403_forbidden_when_role_is_not_superadmin():void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/users/' . $user->id);
        
        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
