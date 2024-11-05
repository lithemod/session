<?php

namespace Tests\Middleware\Session;

use PHPUnit\Framework\TestCase;
use Lithe\Http\Request;
use Lithe\Http\Response;
use function Lithe\Middleware\Session\session;

class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testSessionMiddlewareStartsSessionAndSetsParameters()
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->onlyMethods([])
            ->getMock();
        $responseMock = $this->createMock(Response::class);

        // Create a "next" function that accepts the request and response
        $nextMock = function () {
            return null;
        };

        // Set the session directory path to a temporary location for the test
        $sessionPath = sys_get_temp_dir() . '/test_sessions';
        $options = [
            'path' => $sessionPath,
            'lifetime' => 3600, // 1 hour lifetime
            'domain' => 'localhost',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        // Create the middleware instance
        $middleware = session($options);

        // Execute the middleware
        $middleware($requestMock, $responseMock, $nextMock);

        // Check if the session has been started
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        // Check if the session directory was created
        $this->assertDirectoryExists($sessionPath);

        // Validate session cookie parameters
        $this->assertEquals(3600, ini_get('session.cookie_lifetime'));
        $this->assertEquals('/', ini_get('session.cookie_path'));
        $this->assertEquals($options['domain'], ini_get('session.cookie_domain'));
        $this->assertEquals('0', ini_get('session.cookie_secure')); // '0' means false
        $this->assertEquals('1', ini_get('session.cookie_httponly')); // '1' means true
        $this->assertEquals($options['samesite'], session_get_cookie_params()['samesite']);

        // Clean up after the test
        session_destroy();
        rmdir($sessionPath);
    }
}
