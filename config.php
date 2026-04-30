<?php

// Configuration SMTP pour l'envoi des emails.
// Remplacez ces valeurs par le compte email qui enverra les messages Workify.
// Gmail: host smtp.gmail.com, port 587, encryption tls, password = mot de passe d'application.
// Outlook.com: host smtp-mail.outlook.com, port 587, encryption tls.
if (!defined('MAIL_SMTP_HOST')) {
    define('MAIL_SMTP_HOST', 'smtp.gmail.com');
}
if (!defined('MAIL_SMTP_PORT')) {
    define('MAIL_SMTP_PORT', 587);
}
if (!defined('MAIL_SMTP_ENCRYPTION')) {
    define('MAIL_SMTP_ENCRYPTION', 'tls');
}
if (!defined('MAIL_SMTP_USERNAME')) {
    define('MAIL_SMTP_USERNAME', 'yassinebenromthane21@gmail.com');
}
if (!defined('MAIL_SMTP_PASSWORD')) {
    define('MAIL_SMTP_PASSWORD', 'mblw jqtn uqjt amxq');
}
if (!defined('MAIL_FROM_EMAIL')) {
    define('MAIL_FROM_EMAIL', MAIL_SMTP_USERNAME);
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'Workify');
}

class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=workify_utilisateurs_db',
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
?>
