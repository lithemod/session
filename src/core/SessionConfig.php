<?php

namespace Lithe\Middleware\Session\Core;

/**
 * Class SessionConfig
 * Manages session configuration options.
 */
class SessionConfig {
    /**
     * @var array Configuration options for the session.
     */
    private array $options;

    /**
     * SessionConfig constructor.
     *
     * @param array $options Custom options for session configuration.
     */
    public function __construct(array $options) {
        // Merges default options with the provided options.
        $this->options = array_merge([
            'path' => sys_get_temp_dir() . '/sessions', // Default session storage path.
            'lifetime' => 1440, // Default session lifetime in minutes.
            'domain' => '', // Default domain for the session cookie.
            'secure' => false, // Default secure flag for the cookie.
            'httponly' => true, // Default HTTP only flag for the cookie.
            'samesite' => 'Lax', // Default SameSite attribute for the cookie.
        ], $options);
    }

    /**
     * Get the session storage path.
     *
     * @return string The path where session files will be stored.
     */
    public function getPath(): string {
        return $this->options['path'];
    }

    /**
     * Get the session lifetime.
     *
     * @return int The lifetime of the session in minutes.
     */
    public function getLifetime(): int {
        return $this->options['lifetime'];
    }

    /**
     * Get the domain for the session cookie.
     *
     * @return string The domain associated with the session cookie.
     */
    public function getDomain(): string {
        return $this->options['domain'] ?? '';
    }

    /**
     * Check if the session cookie should be secure.
     *
     * @return bool True if the cookie should be secure, false otherwise.
     */
    public function isSecure(): bool {
        return $this->options['secure'];
    }

    /**
     * Check if the session cookie is HTTP only.
     *
     * @return bool True if the cookie is HTTP only, false otherwise.
     */
    public function isHttpOnly(): bool {
        return $this->options['httponly'];
    }

    /**
     * Get the SameSite attribute for the session cookie.
     *
     * @return string The SameSite attribute value for the session cookie.
     */
    public function getSameSite(): string {
        return $this->options['samesite'];
    }
}
