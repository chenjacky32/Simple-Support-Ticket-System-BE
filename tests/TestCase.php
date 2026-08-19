<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default roles if running feature tests with database
        if (in_array(RefreshDatabase::class, class_uses_recursive($this))) {
            Role::firstOrCreate(['role' => 'USERS']);
            Role::firstOrCreate(['role' => 'SUPERADMIN']);
            Role::firstOrCreate(['role' => 'ADMIN']);
        }
    }

    protected function createAndAuthenticateUser(string $roleName = 'USERS', array $overrides = []): array
    {
        $role = Role::where('role', $roleName)->first();
        
        $user = User::factory()->create(array_merge([
            'role_id' => $role->id,
            'is_active' => true,
        ], $overrides));

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }
}
