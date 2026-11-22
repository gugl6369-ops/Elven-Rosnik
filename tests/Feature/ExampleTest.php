<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_returns_a_successful_response(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_health_endpoint_is_ok(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
    }
}
