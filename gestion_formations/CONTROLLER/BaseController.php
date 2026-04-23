<?php

abstract class BaseController
{
    protected ?PDO $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    protected function render(string $view, array $data = []): void
    {
        $dbConnected = $this->db instanceof PDO;
        $flashMessages = get_flash_messages();
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../VIEW/' . $view . '.php';
    }
}
