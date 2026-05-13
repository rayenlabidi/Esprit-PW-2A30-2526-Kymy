<?php
// config/database.php

$rootConfig = dirname(__DIR__, 2) . '/config.php';
if (file_exists($rootConfig)) {
    require_once $rootConfig;
}

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host    = defined('WORKIFY_DB_HOST') ? WORKIFY_DB_HOST : (getenv('WORKIFY_DB_HOST') ?: 'localhost');
        $dbname  = defined('WORKIFY_DB_NAME') ? WORKIFY_DB_NAME : (getenv('WORKIFY_DB_NAME') ?: 'workify');
        $user    = defined('WORKIFY_DB_USERNAME') ? WORKIFY_DB_USERNAME : (getenv('WORKIFY_DB_USERNAME') ?: 'root');
        $pass    = defined('WORKIFY_DB_PASSWORD') ? WORKIFY_DB_PASSWORD : (getenv('WORKIFY_DB_PASSWORD') ?: '');
        $charset = defined('WORKIFY_DB_CHARSET') ? WORKIFY_DB_CHARSET : (getenv('WORKIFY_DB_CHARSET') ?: 'utf8mb4');

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Never expose real error in production
            error_log($e->getMessage());
            die(json_encode(['error' => 'Database connection failed.']));
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning / unserialization
    private function __clone() {}
    public function __wakeup() {}
}
