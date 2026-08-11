<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Http\Payloads\Users\ListUserPayload;

class UsersUnitTest extends TestCase
{
    /**
     * Test Case Unit Testing
     * 
     * 1. It should be able to return ListUserPayload 
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
}
