<?php

namespace Tests\Middleware\Session;

use PHPUnit\Framework\TestCase;
use Lithe\Http\Request;
use Lithe\Http\Response;
use Lithe\Support\Session;

use function Lithe\Middleware\Session\session;

class SessionTest extends TestCase
{
    // This method is called after each test is executed to clean up the environment.
    protected function tearDown(): void
    {
        // Check if the session is active and destroy it if it is.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Clean up any session directories created for the tests.
        $sessionPath = sys_get_temp_dir() . '/test_sessions';
        if (is_dir($sessionPath)) {
            $this->removeDirectory($sessionPath);
        }
    }

    // This method removes a directory and its contents recursively.
    private function removeDirectory($dir)
    {
        // Return if the directory does not exist.
        if (!is_dir($dir)) {
            return;
        }
        // Scan the directory and exclude '.' and '..'
        $files = array_diff(scandir($dir), ['.', '..']);
        // Iterate over each file and remove it.
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->removeDirectory("$dir/$file") : unlink("$dir/$file");
        }
        // Remove the directory itself after its contents are deleted.
        rmdir($dir);
    }

    // Test to verify that the session middleware starts a session and sets the expected parameters.
    public function testSessionMiddlewareStartsSessionAndSetsParameters()
    {
        // Create mock objects for Request and Response classes.
        $requestMock = $this->createMock(Request::class);
        $responseMock = $this->createMock(Response::class);

        // Create a "next" function that does nothing for this test.
        $nextMock = function () {
            return null;
        };

        // Set the session directory path to a temporary location for the test.
        $sessionPath = sys_get_temp_dir() . '/test_sessions';
        $options = [
            'path' => $sessionPath,
            'lifetime' => 3600, // 1 hour lifetime
            'domain' => 'localhost',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        // Create the session middleware instance with the specified options.
        $middleware = session($options);

        // Execute the middleware with the mock request and response.
        $middleware($requestMock, $responseMock, $nextMock);

        // Assert that the session has been started.
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        // Assert that the session directory was created.
        $this->assertDirectoryExists($sessionPath);

        // Validate session cookie parameters.
        $this->assertEquals(3600, ini_get('session.cookie_lifetime'));
        $this->assertEquals('/', ini_get('session.cookie_path'));
        $this->assertEquals($options['domain'], ini_get('session.cookie_domain'));
        $this->assertEquals('0', ini_get('session.cookie_secure')); // '0' means false
        $this->assertEquals('1', ini_get('session.cookie_httponly')); // '1' means true
        $this->assertEquals($options['samesite'], session_get_cookie_params()['samesite']);

        // Validate the session ID format using a regular expression.
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9,\-]+$/', session_id(), 'Session ID format is invalid.');

        // Clean up after the test by destroying the session and removing the session directory.
        session_destroy();
        $this->removeDirectory($sessionPath);
    }

    // Test to check that session data persists correctly.
    public function testSessionDataPersistence()
    {
        // Create a mock for the Request class without any specific mocked methods.
        $requestMock = $this->getMockBuilder(Request::class)
            ->onlyMethods([]) // Specify methods to be mocked if necessary
            ->getMock();
        $responseMock = $this->createMock(Response::class);

        // Session configurations
        $sessionPath = sys_get_temp_dir() . '/test_sessions';
        $options = [
            'path' => $sessionPath,
            'lifetime' => 3600,
            'domain' => 'localhost',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        // Create the session middleware instance.
        $middleware = session($options);

        // Execute the middleware with the mock request and response.
        $middleware($requestMock, $responseMock, function () {
            // Set a value in the session.
             Session::put('user', 'testUser'); // Ensure this line is included to set session data.
            return null;
        });

        // Check if the session is active.
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        // Verify that the session data was persisted correctly.
        $this->assertEquals('testUser', Session::get('user') ?? null); // Ensure the value was persisted.
    }
}
