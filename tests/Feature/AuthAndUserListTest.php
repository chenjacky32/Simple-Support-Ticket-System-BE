<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndUserListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create initial roles
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'ADMIN']);
    }
    /**
     * Test Case Integration Testing
     *  
        * Auth Group Routing Collection Testing
            * 1. Register
            *  - It should be able to register user
            *  - It should be not able to register with duplicate email
            *  - It should be not able to register with invalid data
        *  
            * 2. Login
            *  - It should be able to login user
            *  - It should be not able to login with invalid credentials
        *  
            * 3. Users List
            *  - It should be not able to access users list without token
            *  - It should be able to access users list with valid token
            *  - It should be able to filter users list by search and status
        *  
     *  
     */ 

    public function test_it_should_be_able_to_register_user(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'confirmPassword' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Register Successfull',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'USERS',
                    'isActive' => true,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_it_should_be_not_able_to_register_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'confirmPassword' => 'secret123',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Email Already Exists',
            ]);
    }

    public function test_it_should_be_not_able_to_register_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'confirmPassword' => 'different',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Validation error',
            ])
            ->assertJsonStructure(['errors' => ['name', 'email', 'password', 'confirmPassword']]);
    }

    public function test_it_should_be_able_to_login_user(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Login Successfull',
            ])
            ->assertJsonStructure(['data' => ['accessToken']]);
    }

    public function test_it_should_be_not_able_to_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_not_able_to_access_users_list_without_token(): void
    {
        $response = $this->getJson('/api/v1/users/list');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_be_able_to_access_users_list_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $token = auth()->guard('api')->login($user);

        // Create some users to paginate
        User::factory()->count(15)->create([
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
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
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        $token = auth()->guard('api')->login($user);

        // Active match
        User::create([
            'name' => 'Target Search Active',
            'email' => 'target.active@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        // Inactive match
        User::create([
            'name' => 'Target Search Inactive',
            'email' => 'target.inactive@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => false,
        ]);

        // Active non-match
        User::create([
            'name' => 'Other Active',
            'email' => 'other.active@example.com',
            'password' => 'secret123',
            'role_id' => Role::where('role', 'USERS')->first()->id,
            'is_active' => true,
        ]);

        // Query status ACTIVE and search "Target"
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
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
}
