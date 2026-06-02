<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_api_health_endpoint_is_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk();
        $response->assertExactJson([
            'status' => 'ok',
        ]);
    }
}
