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

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'SUPERADMIN']);
    }

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
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'SUPERADMIN')->first()->id,
            'is_active' => false,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
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
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => false,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
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
