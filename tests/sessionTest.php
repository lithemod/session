<?php

namespace Tests\Middleware\Session;

use PHPUnit\Framework\TestCase;
use Lithe\Http\Request;
use Lithe\Http\Response;

use function Lithe\Middleware\Session\session;

class sessionTest extends TestCase
{
    public function testSessionMiddlewareStartsSessionAndSetsParameters()
    {
        $requestMock = $this->createMock(Request::class);
        $responseMock = $this->createMock(Response::class);

        // Create a "next" function that simply returns null
        $nextMock = function () {};

        // Set the session directory path to a temporary location for the test
        $options = [
            'path' => sys_get_temp_dir() . '/test_sessions',
        ];

        // Create the middleware instance
        $middleware = session($options);

        // Execute the middleware
        $middleware($requestMock, $responseMock, $nextMock);

        // Check if the session has been started
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        // Check if the session directory was created
        $this->assertDirectoryExists($options['path']);

        // Clean up after the test
        session_destroy();
        rmdir($options['path']);
    }
}