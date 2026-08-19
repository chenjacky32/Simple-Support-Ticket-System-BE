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
            * 1. Login
            *  - It should be able to login user
            *  - It should be not able to login with invalid credentials
     *  
     */ 


class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create initial roles
        Role::create(['role' => 'USERS']);
        Role::create(['role' => 'ADMIN']);
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
}
