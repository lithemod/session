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
     * @throws \InvalidArgumentException if the session ID contains illegal characters.
     */
    public function write($sessionId, $data): bool {
        // Verifica se o ID da sessão contém apenas caracteres válidos.
        if (!$this->isValidSessionId($sessionId)) {
            throw new \InvalidArgumentException("Session ID contains illegal characters.");
        }

        // Cria um subdiretório baseado nos primeiros dois caracteres do ID da sessão.
        $dir = sprintf('%s/%s/%s', $this->basePath, $sessionId[0], $sessionId[1]);

        // Verifica se o diretório existe, cria se não existir.
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Define o caminho do arquivo para os dados da sessão.
        $filePath = "$dir/sess_$sessionId";

        // Chama o método pai para salvar os dados da sessão.
        return parent::write($filePath, $data);
    }

    /**
     * Verifica se o ID da sessão é válido.
     * 
     * @param string $sessionId ID da sessão a ser validado.
     * @return bool Retorna true se o ID for válido, caso contrário, false.
     */
    private function isValidSessionId(string $sessionId): bool {
        // Permite apenas caracteres alfanuméricos e os símbolos '-', e ','.
        return preg_match('/^[A-Za-z0-9,\-]+$/', $sessionId) === 1;
    }
}
