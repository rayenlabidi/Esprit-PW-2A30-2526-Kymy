<?php

$localConfigPath = __DIR__ . '/config.local.php';
if (file_exists($localConfigPath)) {
  require_once $localConfigPath;
}

if (!defined('WORKIFY_MAIL_HOST')) {
  define('WORKIFY_MAIL_HOST', 'smtp.gmail.com');
}

if (!defined('WORKIFY_MAIL_PORT')) {
  define('WORKIFY_MAIL_PORT', 587);
}

if (!defined('WORKIFY_MAIL_USERNAME')) {
  define('WORKIFY_MAIL_USERNAME', 'rayanlabidi.rl@gmail.com');
}

if (!defined('WORKIFY_MAIL_PASSWORD')) {
  define('WORKIFY_MAIL_PASSWORD', '');
}

if (!defined('WORKIFY_MAIL_FROM')) {
  define('WORKIFY_MAIL_FROM', WORKIFY_MAIL_USERNAME);
}

if (!defined('WORKIFY_MAIL_FROM_NAME')) {
  define('WORKIFY_MAIL_FROM_NAME', 'Workify');
}



class config

{

  private static $pdo = null;



  public static function getConnexion()

  {

    if (!isset(self::$pdo)) {

      try {

        self::$pdo = new PDO(

          'mysql:host=localhost;dbname=2a30',

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

