<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Responses\JsonDataResponse;
use App\Http\Payloads\Auth\RegisterPayload;
use App\Http\Payloads\Auth\LoginPayload;


class AuthUnitTest extends TestCase
{
    /**
     * Test Case Unit Testing
     *
     * Testing API Response Contract
     *  
     * 1. It should be able to return error 401 based on api contract
     * 2. It should be able to return error 403 based on api contract
     * 3. It should be able to return error 404 based on api contract
     * 4. It should be able to return error 409 based on api contract
     * 5. It should be able to return error 422 based on api contract
     * 6. It should be able to return error 500 based on api contract
     * 7. It should be able to return login successfull response based on api contract
     * 8. It should be able to return register successfull response based on api contract
     * 
     * Testing Payload
     *  
     * 1. It should be able to return register payload
     * 2. It should be able to return login payload
     */
    public function test_it_should_be_able_return_error_401_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: 'Invalid credentials',
            status: 401,
        );

        // Assertion
        $this->assertEquals('fail', $response->getData(true)['status']);
        $this->assertEquals('Invalid credentials', $response->getData(true)['message']);
        $this->assertEquals(401, $response->status());
    }

    public function test_it_should_be_able_return_error_403_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: "You don't have permission to access this resource",
            status: 403,
        );

        // Assertion
        $this->assertEquals('fail', $response->getData(true)['status']);
        $this->assertEquals("You don't have permission to access this resource", $response->getData(true)['message']);
        $this->assertEquals(403, $response->status());
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_it_should_be_able_return_error_404_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: 'Resource not found',
            status: 404,
        );

        // Assertion
        $this->assertEquals('fail', $response->getData(true)['status']);
        $this->assertEquals('Resource not found', $response->getData(true)['message']);
        $this->assertEquals(404, $response->status());
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_it_should_be_able_return_error_409_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: 'Email Already Exists',
            status: 409,
        );

        // Assertion
        $this->assertEquals('fail', $response->getData(true)['status']);
        $this->assertEquals('Email Already Exists', $response->getData(true)['message']);
        $this->assertEquals(409, $response->status());
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_it_should_be_able_return_error_422_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: 'Validation error',
            status: 422,
        );

        // Assertion
        $this->assertEquals('fail', $response->getData(true)['status']);
        $this->assertEquals('Validation error', $response->getData(true)['message']);
        $this->assertEquals(422, $response->status());
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_it_should_be_able_return_error_500_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            message: 'Internal server error',
            status: 500,
        );

        // Assertion
        $this->assertEquals('error', $response->getData(true)['status']);
        $this->assertEquals('Internal server error', $response->getData(true)['message']);
        $this->assertEquals(500, $response->status());
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_it_should_be_able_return_login_success_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            data: [
                'accessToken' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9',
            ],
            message: 'Login Successfull',
            status: 200,
        );

        // Assertion
        $this->assertEquals('success', $response->getData(true)['status']);
        $this->assertEquals('Login Successfull', $response->getData(true)['message']);
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9', $response->getData(true)['data']['accessToken']);
        $this->assertEquals(200, $response->status());
    }

    public function test_it_should_be_able_return_register_success_based_on_api_contract(): void
    {
        $response = new JsonDataResponse(
            data: [
                'name' => 'testing123',
                'email' => 'example@example.com',
                'role' => 'USERS',
                'isActive' => true,
            ],
            message: 'Register Successfull',
            status: 200,
        );

        // Assertion
        $this->assertEquals('success', $response->getData(true)['status']);
        $this->assertEquals('Register Successfull', $response->getData(true)['message']);
        $this->assertEquals('testing123', $response->getData(true)['data']['name']);
        $this->assertEquals('example@example.com', $response->getData(true)['data']['email']);
        $this->assertEquals('USERS', $response->getData(true)['data']['role']);
        $this->assertTrue($response->getData(true)['data']['isActive']);
        $this->assertEquals(200, $response->status());
    }

    public function test_it_should_be_able_return_register_payload(): void
    {
        // Initialize Register Payload
        $payload = new RegisterPayload(
            name: 'testing123',
            email: 'example@example.com',
            password: 'password',
        );

        // Assertion for properties
        $this->assertEquals('testing123', $payload->name);
        $this->assertEquals('example@example.com', $payload->email);
        $this->assertEquals('password', $payload->password);

        // Assertion for toArray() method
        $expectedArray = [
            'name' => 'testing123',
            'email' => 'example@example.com',
            'password' => 'password',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_return_login_payload(): void
    {
        // Initialize Login Payload
        $payload = new LoginPayload(
            email: 'example@example.com',
            password: 'password',
        );

        // Assertion
        $this->assertEquals('example@example.com', $payload->email);
        $this->assertEquals('password', $payload->password);

        $expectedArray = [
            'email' => 'example@example.com',
            'password' => 'password',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }
}
