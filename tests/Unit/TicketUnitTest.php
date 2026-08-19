<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Payloads\Tickets\CreateTicketPayload;
use App\Http\Payloads\Tickets\ListTicketPayload;
use App\Http\Payloads\Tickets\ReplyTicketPayload;
use App\Http\Payloads\Tickets\UpdateTicketStatusPayload;

class TicketUnitTest extends TestCase
{
    /**
     *  
     * Test Case Unit Testing
     * 
     * 1. It should be able to return CreateTicketPayload
     * 2. It should be able to return ListTicketPayload
     * 3. It should be able to return ReplyTicketPayload
     * 4. It should be able to return UpdateTicketStatusPayload
     */
    public function test_it_should_be_able_to_return_create_ticket_payload(): void
    {
        // Initialize CreateTicketPayload
        $payload = new CreateTicketPayload(
            title: 'Bug Report',
            description: 'Application crashes on login',
            attachmentPath: '/uploads/logs.txt'
        );

        // Assertion for properties
        $this->assertEquals('Bug Report', $payload->title);
        $this->assertEquals('Application crashes on login', $payload->description);
        $this->assertEquals('/uploads/logs.txt', $payload->attachmentPath);

        // Assertion for toArray() method
        $expectedArray = [
            'title' => 'Bug Report',
            'description' => 'Application crashes on login',
            'attachmentPath' => '/uploads/logs.txt',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_to_return_list_ticket_payload(): void
    {
        // Initialize ListTicketPayload
        $payload = new ListTicketPayload(
            page: 1,
            size: 15,
            status: 'OPENED',
            startDate: '2026-01-01',
            endDate: '2026-01-31',
            search: 'login'
        );

        // Assertion for properties
        $this->assertEquals(1, $payload->page);
        $this->assertEquals(15, $payload->size);
        $this->assertEquals('OPENED', $payload->status);
        $this->assertEquals('2026-01-01', $payload->startDate);
        $this->assertEquals('2026-01-31', $payload->endDate);
        $this->assertEquals('login', $payload->search);

        // Assertion for toArray() method
        $expectedArray = [
            'page' => 1,
            'size' => 15,
            'status' => 'OPENED',
            'startDate' => '2026-01-01',
            'endDate' => '2026-01-31',
            'search' => 'login',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_to_return_reply_ticket_payload(): void
    {
        // Initialize ReplyTicketPayload
        $payload = new ReplyTicketPayload(
            message: 'We are looking into this issue.'
        );

        // Assertion for properties
        $this->assertEquals('We are looking into this issue.', $payload->message);

        // Assertion for toArray() method
        $expectedArray = [
            'message' => 'We are looking into this issue.',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }

    public function test_it_should_be_able_to_return_update_ticket_status_payload(): void
    {
        // Initialize UpdateTicketStatusPayload
        $payload = new UpdateTicketStatusPayload(
            status: 'RESOLVED'
        );

        // Assertion for properties
        $this->assertEquals('RESOLVED', $payload->status);

        // Assertion for toArray() method
        $expectedArray = [
            'status' => 'RESOLVED',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }
}
