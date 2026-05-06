<?php
/* ============================================================
   ChatbotAPI.php  –  Server-side proxy for Gemini API
   Keeps the API key hidden from the client.
   ============================================================ */

header('Content-Type: application/json; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ── API Configuration ──────────────────────────────────────────
$GEMINI_API_KEY  = 'AIzaSyBZm1cF5Pqxe7xqp7R6SlGtJ75fFpj70tw';
$GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

$SYSTEM_PROMPT =
    'You are a helpful assistant for a freelancing platform called Workify. ' .
    'Help users with publications, messages, and navigation. ' .
    'Keep answers concise and friendly.';

// ── Read incoming JSON body ────────────────────────────────────
$rawBody  = file_get_contents('php://input');
$incoming = json_decode($rawBody, true);

if (!$incoming || empty($incoming['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing "message" field']);
    exit;
}

$userMessage = trim($incoming['message']);
$fullPrompt  = $SYSTEM_PROMPT . "\n\nUser: " . $userMessage;

// ── Build Gemini request payload ───────────────────────────────
$payload = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => $fullPrompt]
            ]
        ]
    ]
]);

// ── Call Gemini API via cURL ───────────────────────────────────
$ch = curl_init($GEMINI_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'X-goog-api-key: ' . $GEMINI_API_KEY,
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false, // Set to false to avoid local XAMPP certificate issues
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// ── Handle errors ──────────────────────────────────────────────
if ($curlError) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach Gemini API', 'detail' => $curlError]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Gemini API returned an error', 'detail' => json_decode($response, true)]);
    exit;
}

// ── Extract reply text ─────────────────────────────────────────
$data = json_decode($response, true);

if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $data['candidates'][0]['content']['parts'][0]['text'];
} else {
    // If the expected structure is missing, return what we actually got so we can debug it
    $reply = "Sorry, I could not generate a response right now. Debug info: " . json_encode($data);
}

echo json_encode(['reply' => $reply]);
