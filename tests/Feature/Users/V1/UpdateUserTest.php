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
            * 1. Update User 
            *  - It should be not able to update user without access token
            *  - It should be able to update user with valid access token
            *  - It should be not able to update user data when email is already exist in other user
            * 2. Protected Role (SUPERADMIN only)
            *  - It should be throw response 403 Forbidden when role is not SUPERADMIN
     *  
     */ 

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'SUPERADMIN']);
    }

    public function test_it_should_be_not_able_to_update_user_without_access_token(): void
    {
        $response = $this->putJson('/api/v1/users/123e4567');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_update_user_with_valid_access_token(): void
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
            ->putJson('/api/v1/users/' . $user->id, [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
                'password' => 'newpassword',
                'role' => 'USERS',
                'isActive' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'user details updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => 'Updated User',
                    'email' => 'updated@example.com',
                    'role' => 'USERS',
                    'isActive' => false,
                ],
            ]);
    }

    public function test_it_should_be_not_able_to_update_user_data_when_email_is_already_exist_in_other_user(): void
    {
        $findRoleId = Role::where('role', 'SUPERADMIN')->first();

        $firstUser = User::create([
            'name' => 'First User',
            'email' => 'firstuser@example.com',
            'password' => 'secret123',
            'role_id' => $findRoleId->id,
            'is_active' => false,
        ]);

        $secondUser = User::create([
            'name' => 'Second User',
            'email' => 'seconduser@example.com',
            'password' => 'secret123',
            'role_id' => $findRoleId->id,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($secondUser);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/users/' . $secondUser->id, [
                'name' => 'Second User',
                'email' => 'firstuser@example.com',
                'role' => 'SUPERADMIN',
                'isActive' => false,
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Email Already Exists',
            ]);
    }

    public function test_it_should_be_throw_response_403_Forbidden_when_role_is_not_superadmin(): void
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
            ->putJson('/api/v1/users/' . $user->id, [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
                'password' => 'newpassword',
                'role' => 'USERS',
                'isActive' => false,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'fail',
                'message' => 'You don\'t have permission to access this resource',
            ]);
    }
}
