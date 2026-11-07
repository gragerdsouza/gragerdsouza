<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case: disables Vite so views render without built assets.
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
    }
}
