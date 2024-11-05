<?php

namespace Lithe\Middleware\Session\Core;

use SessionHandler;

/**
 * Class DirectoryBasedSessionStore
 * Custom session handler that stores session data in a directory structure
 * based on the session ID.
 */
class DirectoryBasedSessionStore extends SessionHandler {
    /**
     * @var string Base path for storing session files.
     */
    private string $basePath;

    /**
     * DirectoryBasedSessionStore constructor.
     * 
     * @param string $path Base path where session files will be stored.
     */
    public function __construct(string $path) {
        $this->basePath = $path;
    }

    /**
     * Write session data to a file.
     * 
     * @param string $sessionId ID of the session.
     * @param string $data Data to be stored in the session.
     * @return bool Returns true on success, false on failure.
     */
    public function write($sessionId, $data): bool {
        // Create a subdirectory based on the first two characters of the session ID.
        $dir = sprintf('%s/%s/%s', $this->basePath, $sessionId[0], $sessionId[1]);

        // Check if the directory exists, create it if it doesn't.
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Define the file path for the session data.
        $filePath = "$dir/sess_$sessionId";

        // Call the parent's write method to save the session data.
        return parent::write($filePath, $data);
    }
}