<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Payloads\Dashboard\GetDashboardStatPayload;

class DashboardUnitTest extends TestCase
{
    /**
     * Test Case Unit Testing
     * 
     * 1. It should be able to return GetDashboardStatPayload
     * 
     */
    public function test_it_should_be_able_to_return_get_dashboard_stat_payload(): void
    {
        // Initialize GetDashboardStatPayload
        $payload = new GetDashboardStatPayload(
            startDate: '2026-01-01',
            endDate: '2026-01-31'
        );

        // Assertion for properties
        $this->assertEquals('2026-01-01', $payload->startDate);
        $this->assertEquals('2026-01-31', $payload->endDate);

        // Assertion for toArray() method
        $expectedArray = [
            'startDate' => '2026-01-01',
            'endDate' => '2026-01-31',
        ];
        $this->assertEquals($expectedArray, $payload->toArray());
    }
}
