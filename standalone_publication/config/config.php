<?php
$rootConfig = dirname(__DIR__, 2) . '/config.php';

if (file_exists($rootConfig)) {
    require_once $rootConfig;
}

if (!class_exists('config', false)) {
    class config
    {
        private static $pdo = null;

        public static function getConnexion()
        {
            if (!isset(self::$pdo)) {
                try {
                    self::$pdo = new PDO(
                        'mysql:host=localhost;dbname=workify;charset=utf8mb4',
                        'root',
                        '',
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]
                    );
                } catch (Exception $e) {
                    die('Erreur: ' . $e->getMessage());
                }
            }

            return self::$pdo;
        }
    }
}
