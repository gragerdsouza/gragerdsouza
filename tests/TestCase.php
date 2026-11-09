<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

/**
 * Base test case: disables Vite so views render without built assets, and fails any
 * test that makes a real outbound HTTP request (fake it with Http::fake() instead).
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Prepare the application for each test.
     *
     * @return void Nothing is returned.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Http::preventStrayRequests();
    }
}
