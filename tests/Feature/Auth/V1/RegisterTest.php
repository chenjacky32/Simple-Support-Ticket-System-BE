<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
     * Test Case Integration Testing
     *  
        * Auth Group Routing Collection Testing
            * 1. Register
            *  - It should be able to register user
            *  - It should be not able to register with duplicate email
            *  - It should be not able to register with invalid data
     *  
     */ 

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create initial roles
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'ADMIN']);
    }

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
                    'isActive' => false,
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
}
