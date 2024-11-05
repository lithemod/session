<?php
namespace Lithe\Middleware\Session\Core;

use SessionHandler;

/**
 * Class DirectoryBasedSessionStore
 * Custom session handler that stores session data in a directory structure
 * based on the session ID.
 */
class DirectoryBasedSessionStore extends SessionHandler {
    private string $basePath;

    /**
     * DirectoryBasedSessionStore constructor.
     * 
     * @param string $path Base path where session files will be stored.
     * This ensures that a trailing slash is present in the base path.
     */
    public function __construct(string $path) {
        $this->basePath = rtrim($path, '/') . '/'; // Ensure the trailing slash is present
    }

    /**
     * Write session data to a file.
     * 
     * @param string $sessionId ID of the session.
     * @param string $data Data to be stored in the session.
     * @return bool Returns true on success, false on failure.
     * @throws \InvalidArgumentException if the session ID contains illegal characters.
     */
    public function write($sessionId, $data): bool {
        // Check if the session ID contains only valid characters.
        if (!$this->isValidSessionId($sessionId)) {
            throw new \InvalidArgumentException("Session ID contains illegal characters.");
        }

        // Create a subdirectory based on the first two characters of the session ID.
        $dir = sprintf('%s/%s/%s', $this->basePath, $sessionId[0], $sessionId[1]);

        // Check if the directory exists; create it if it does not.
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Define the file path for the session data.
        $filePath = "$dir/sess_$sessionId";

        // Write the session data.
        return file_put_contents($filePath, $data) !== false;
    }

    /**
     * Read session data from a file.
     * 
     * @param string $sessionId ID of the session.
     * @return string Returns session data if the file exists, or an empty string if it does not.
     */
    public function read($sessionId): string {
        // Construct the file path to read session data.
        $filePath = sprintf('%s/%s/%s/sess_%s', $this->basePath, $sessionId[0], $sessionId[1], $sessionId);
        
        // Return the session data if the file exists.
        return file_exists($filePath) ? file_get_contents($filePath) : '';
    }

    /**
     * Check if the session ID is valid.
     * 
     * @param string $sessionId ID of the session to be validated.
     * @return bool Returns true if the ID is valid; otherwise, false.
     */
    private function isValidSessionId(string $sessionId): bool {
        // Allow only alphanumeric characters and the symbols '-', and ','.
        return preg_match('/^[A-Za-z0-9,\-]+$/', $sessionId) === 1;
    }
}
