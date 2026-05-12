<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../lib/CaptchaService.php';

$challenge = CaptchaService::getChallenge();
$code = ($challenge['type'] ?? '') === 'text' ? ($challenge['code'] ?? '') : '';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if ($code === '') {
    http_response_code(404);
    echo json_encode(['error' => 'Captcha indisponible.']);
    exit;
}

$parts = [];

for ($i = 0; $i < strlen($code); $i++) {
    $character = $code[$i];

    if (ctype_digit($character)) {
        $parts[] = 'number ' . $character;
    } elseif (ctype_upper($character)) {
        $parts[] = 'capital ' . $character;
    } else {
        $parts[] = 'lowercase ' . $character;
    }
}

echo json_encode([
    'speech' => implode(', ', $parts)// Il renvoie un JSON avec une phrase lisible par le navigateur.
]);
