<?php

namespace Lithe\Middleware\Session;

use Closure;
use Exception;
use Lithe\Middleware\Session\Core\DirectoryBasedSessionStore;
use Lithe\Middleware\Session\Core\SessionConfig;
use Lithe\Support\Log;

/**
 * Middleware responsible for managing session.
 *
 * @param array $options Options for session management.
 *   - 'lifetime' (int): Lifetime of the session in seconds (default: 2592000).
 *   - 'domain' (string|null): Domain for which the session cookie is valid (default: null).
 *   - 'secure' (bool): Indicates if the session cookie should only be sent over secure connections (default: false).
 *   - 'httponly' (bool): Indicates if the session cookie should be accessible only through HTTP requests (default: true).
 *   - 'samesite' (string): SameSite attribute for session cookie to prevent CSRF attacks (default: 'Lax').
 *   - 'path' (string): Directory path where session files will be stored (default: 'storage/framework/session').
 * @return \Closure Middleware function that handles session management.
 */
function session(array $options = [])
{
    // Default options
    $defaultOptions = [
        'lifetime' => 2592000,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
        'path' => dirname(__DIR__, 4) . '/storage/framework/session',
    ];

    // Merge user options with default options
    $options = array_merge($defaultOptions, $options);

    // Create SessionConfig instance
    $config = new SessionConfig($options);
    
    // Get the session save path
    $savePath = realpath($config->getPath()) ?: $config->getPath();

    return function (\Lithe\Http\Request $req, \Lithe\Http\Response $res, callable $next) use ($config, $savePath) {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                // Set the session save handler
                session_set_save_handler(new DirectoryBasedSessionStore($savePath), true);

                // Check if the session storage path exists, if not create it
                if (!is_dir($savePath)) {
                    mkdir($savePath, 0755, true);
                }

                // Set the session save path
                session_save_path($savePath);

                // Configure session lifetime
                ini_set("session.gc_maxlifetime", $config->getLifetime());
                ini_set("session.cookie_lifetime", $config->getLifetime());

                // Set session cookie parameters
                session_set_cookie_params([
                    'lifetime' => $config->getLifetime(),
                    'path' => '/',
                    'domain' => $config->getDomain(),
                    'secure' => $config->isSecure(),
                    'httponly' => $config->isHttpOnly(),
                    'samesite' => $config->getSameSite(),
                ]);

                // Start the session
                if (!session_start()) {
                    throw new Exception('Failed to initialize the session.');
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            Log::error($e->getMessage());
        }

        // Assign session object to request
        $req->session = new \Lithe\Support\Session;

        // Continue to the next middleware
        return $next();
    };
}
