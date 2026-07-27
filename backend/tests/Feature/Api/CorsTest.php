<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class CorsTest extends TestCase
{
    private const FRONTEND_ORIGIN = 'http://localhost:3000';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'cors.allowed_origins',
            [self::FRONTEND_ORIGIN],
        );

        config()->set(
            'cors.supports_credentials',
            false,
        );
    }

    public function test_it_allows_preflight_requests_from_the_configured_frontend(): void
    {
        $response = $this
            ->withHeaders([
                'Origin' => self::FRONTEND_ORIGIN,
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'content-type',
            ])
            ->options('/api/v1/vacancies');

        $response
            ->assertNoContent()
            ->assertHeader(
                'Access-Control-Allow-Origin',
                self::FRONTEND_ORIGIN,
            )
            ->assertHeaderMissing(
                'Access-Control-Allow-Credentials',
            );
    }

    public function test_it_does_not_allow_an_unconfigured_origin(): void
    {
        $unconfiguredOrigin = 'https://malicious.example';

        $response = $this
            ->withHeaders([
                'Origin' => $unconfiguredOrigin,
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'content-type',
            ])
            ->options('/api/v1/vacancies');

        $response
            ->assertNoContent()
            ->assertHeader(
                'Access-Control-Allow-Origin',
                self::FRONTEND_ORIGIN,
            )
            ->assertHeaderMissing(
                'Access-Control-Allow-Credentials',
            );

        $this->assertNotSame(
            $unconfiguredOrigin,
            $response->headers->get(
                'Access-Control-Allow-Origin',
            ),
        );
    }
}
