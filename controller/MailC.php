<?php
include_once __DIR__ . '/../config.php';

class MailC
{
    private static $lastError = '';

    public static function getLastError()
    {
        return self::$lastError;
    }

    public static function send($to, $subject, $bodyText)
    {
        self::$lastError = '';

        $to = trim((string)$to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Adresse destinataire invalide.';
            return false;
        }

        $subject = trim((string)$subject);
        if ($subject === '') {
            $subject = 'Notification';
        }

        $bodyText = (string)$bodyText;
        if ($bodyText === '') {
            $bodyText = 'Notification';
        }

        $host = trim((string)MAIL_SMTP_HOST);
        $port = (int)MAIL_SMTP_PORT;
        $encryption = strtolower(trim((string)MAIL_SMTP_ENCRYPTION));
        $username = trim((string)MAIL_SMTP_USERNAME);
        $password = (string)MAIL_SMTP_PASSWORD;
        $fromEmail = trim((string)MAIL_FROM_EMAIL);
        $fromName = trim((string)MAIL_FROM_NAME);

        if (stripos($host, 'gmail.com') !== false) {
            $password = str_replace(' ', '', $password);
        }

        if ($host === '' || $port <= 0 || $username === '' || $password === '' || strpos($username, 'votre-adresse') !== false) {
            self::$lastError = 'Configuration SMTP incomplete dans config.php.';
            return false;
        }

        if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $fromEmail = $username;
        }

        return self::sendSmtp($host, $port, $encryption, $username, $password, $fromEmail, $fromName, $to, $subject, $bodyText);
    }

    private static function sendSmtp($host, $port, $encryption, $username, $password, $fromEmail, $fromName, $to, $subject, $bodyText)
    {
        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);

        if (!$socket) {
            self::$lastError = 'Connexion SMTP impossible: ' . ($errstr ?: 'serveur inaccessible');
            return false;
        }

        stream_set_timeout($socket, 20);

        if (!self::expect($socket, [220])) {
            fclose($socket);
            return false;
        }

        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
        if (!self::command($socket, 'EHLO ' . $serverName, [250])) {
            fclose($socket);
            return false;
        }

        if ($encryption === 'tls') {
            if (!self::command($socket, 'STARTTLS', [220])) {
                fclose($socket);
                return false;
            }

            if (!function_exists('stream_socket_enable_crypto') || !@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                self::$lastError = 'Le chiffrement TLS SMTP a echoue. Verifiez que l extension OpenSSL de PHP est active.';
                fclose($socket);
                return false;
            }

            if (!self::command($socket, 'EHLO ' . $serverName, [250])) {
                fclose($socket);
                return false;
            }
        }

        if (!self::command($socket, 'AUTH LOGIN', [334])) {
            fclose($socket);
            return false;
        }
        if (!self::command($socket, base64_encode($username), [334])) {
            fclose($socket);
            return false;
        }
        if (!self::command($socket, base64_encode($password), [235])) {
            fclose($socket);
            return false;
        }

        if (!self::command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250])) {
            fclose($socket);
            return false;
        }
        if (!self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251])) {
            fclose($socket);
            return false;
        }
        if (!self::command($socket, 'DATA', [354])) {
            fclose($socket);
            return false;
        }

        $message = self::buildMessage($fromEmail, $fromName, $to, $subject, $bodyText);
        fwrite($socket, self::dotStuff($message) . "\r\n.\r\n");

        if (!self::expect($socket, [250])) {
            fclose($socket);
            return false;
        }

        self::command($socket, 'QUIT', [221]);
        fclose($socket);
        return true;
    }

    private static function command($socket, $command, $expectedCodes)
    {
        fwrite($socket, $command . "\r\n");
        return self::expect($socket, $expectedCodes);
    }

    private static function expect($socket, $expectedCodes)
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }

        $meta = stream_get_meta_data($socket);
        if (!empty($meta['timed_out'])) {
            self::$lastError = 'Timeout SMTP.';
            return false;
        }

        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            self::$lastError = 'Erreur SMTP: ' . trim($response);
            return false;
        }

        return true;
    }

    private static function buildMessage($fromEmail, $fromName, $to, $subject, $bodyText)
    {
        $headers = [];
        $headers[] = 'Date: ' . date(DATE_RFC2822);
        $headers[] = 'From: ' . self::formatAddress($fromEmail, $fromName);
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . self::encodeHeader($subject);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';

        return implode("\r\n", $headers) . "\r\n\r\n" . str_replace(["\r\n", "\r"], "\n", $bodyText);
    }

    private static function formatAddress($email, $name)
    {
        $name = trim((string)$name);
        if ($name === '') {
            return '<' . $email . '>';
        }

        return self::encodeHeader($name) . ' <' . $email . '>';
    }

    private static function encodeHeader($value)
    {
        return '=?UTF-8?B?' . base64_encode((string)$value) . '?=';
    }

    private static function dotStuff($message)
    {
        $message = str_replace(["\r\n", "\r"], "\n", $message);
        $lines = explode("\n", $message);

        foreach ($lines as &$line) {
            if (isset($line[0]) && $line[0] === '.') {
                $line = '.' . $line;
            }
        }

        return implode("\r\n", $lines);
    }
}

?>
