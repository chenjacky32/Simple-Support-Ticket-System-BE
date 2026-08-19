<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Payloads\Users\ListUserPayload;
use App\Http\Payloads\Users\UpdateUserPayload;
use App\Http\Payloads\Users\UpdateUserStatusPayload;

class UsersUnitTest extends TestCase
{
    /**
     * Test Case Unit Testing
     * 
     * 1. It should be able to return ListUserPayload 
     * 2. It should be able to return UpdateUserPayload
     * 3. It should be able to return UpdateUserStatusPayload
     */
    public function test_it_should_be_able_return_list_user_payload(): void
    {
        // Initialize ListUserPayload
        $payload = new ListUserPayload(
            page: 1,
            size: 10,
            status: 'ACTIVE',
            search: 'jacky'
        );

        // Assertion for properties
        $this->assertEquals(1, $payload->page);
        $this->assertEquals(10, $payload->size);
        $this->assertEquals('ACTIVE', $payload->status);
        $this->assertEquals('jacky', $payload->search);

        // Assertion for toArray() method
        $expectedArray = [
            'page' => 1,
            'size' => 10,
            'status' => 'ACTIVE',
            'search' => 'jacky',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_return_update_user_payload(): void
    {
        // Initialize UpdateUserPayload
        $payload = new UpdateUserPayload(
            name: 'John Doe',
            email: 'john@example.com',
            role: 'ADMIN',
            isActive: true
        );

        // Assertion for properties
        $this->assertEquals('John Doe', $payload->name);
        $this->assertEquals('john@example.com', $payload->email);
        $this->assertEquals('ADMIN', $payload->role);
        $this->assertTrue($payload->isActive);

        // Assertion for toArray() method
        $expectedArray = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'ADMIN',
            'isActive' => true,
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_return_update_user_status_payload(): void
    {
        // Initialize UpdateUserStatusPayload
        $payload = new UpdateUserStatusPayload(
            isActive: false
        );

        // Assertion for properties
        $this->assertFalse($payload->isActive);

        // Assertion for toArray() method
        $expectedArray = [
            'isActive' => false,
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }
}
