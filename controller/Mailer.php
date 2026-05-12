<?php
require_once __DIR__ . '/../config.php';

class WorkifyMailer
{
    private $socket;
    private $lastResponse = '';

    public function send($to, $subject, $htmlMessage, $textMessage = '')
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (WORKIFY_MAIL_PASSWORD === '') {
            return false;
        }

        $from = WORKIFY_MAIL_FROM;
        $fromName = WORKIFY_MAIL_FROM_NAME;
        $message = $this->buildMessage($from, $fromName, $to, $subject, $htmlMessage, $textMessage);

        $this->socket = @stream_socket_client(
            'tcp://' . WORKIFY_MAIL_HOST . ':' . WORKIFY_MAIL_PORT,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT
        );

        if (!$this->socket) {
            return false;
        }

        stream_set_timeout($this->socket, 15);

        $ok = $this->expect([220])
            && $this->command('EHLO localhost', [250])
            && $this->command('STARTTLS', [220])
            && $this->enableCrypto()
            && $this->command('EHLO localhost', [250])
            && $this->command('AUTH LOGIN', [334])
            && $this->command(base64_encode(WORKIFY_MAIL_USERNAME), [334])
            && $this->command(base64_encode(WORKIFY_MAIL_PASSWORD), [235])
            && $this->command('MAIL FROM:<' . $from . '>', [250])
            && $this->command('RCPT TO:<' . $to . '>', [250, 251])
            && $this->command('DATA', [354])
            && $this->sendData($message);

        $this->command('QUIT', [221]);
        fclose($this->socket);
        return $ok;
    }

    private function buildMessage($from, $fromName, $to, $subject, $htmlMessage, $textMessage)
    {
        $boundary = 'workify_' . bin2hex(random_bytes(12));
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $safeFromName = str_replace(["\r", "\n"], '', $fromName);
        $safeTo = str_replace(["\r", "\n"], '', $to);

        if ($textMessage === '') {
            $textMessage = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlMessage));
        }

        $headers = [
            'Date: ' . date('r'),
            'From: ' . $safeFromName . ' <' . $from . '>',
            'To: <' . $safeTo . '>',
            'Subject: ' . $encodedSubject,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"'
        ];

        $body = [];
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/plain; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $textMessage;
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/html; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $htmlMessage;
        $body[] = '--' . $boundary . '--';

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
    }

    private function enableCrypto()
    {
        return stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT) === true;
    }

    private function command($command, $expectedCodes)
    {
        fwrite($this->socket, $command . "\r\n");
        return $this->expect($expectedCodes);
    }

    private function sendData($message)
    {
        $message = str_replace("\n.", "\n..", $message);
        fwrite($this->socket, $message . "\r\n.\r\n");
        return $this->expect([250]);
    }

    private function expect($expectedCodes)
    {
        $response = '';

        while (!feof($this->socket)) {
            $line = fgets($this->socket, 515);
            if ($line === false) {
                break;
            }

            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }

        $this->lastResponse = $response;
        $code = (int) substr($response, 0, 3);
        return in_array($code, $expectedCodes);
    }
}
?>
