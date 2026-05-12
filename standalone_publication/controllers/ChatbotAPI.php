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
$GEMINI_API_KEY = '';

// Strategy 1: Load from .env file relative to this controller
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    // Try parse_ini_file first
    $envVars = @parse_ini_file($envFile);
    if (is_array($envVars) && !empty($envVars['GEMINI_API_KEY'])) {
        $GEMINI_API_KEY = trim($envVars['GEMINI_API_KEY']);
    }
    
    // Fallback: manual line-by-line parsing
    if (empty($GEMINI_API_KEY)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line[0] === '#') continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    if (trim($key) === 'GEMINI_API_KEY') {
                        $GEMINI_API_KEY = trim($value);
                        break;
                    }
                }
            }
        }
    }
}

// Strategy 2: Check environment variable (set via Apache/php.ini)
if (empty($GEMINI_API_KEY) && getenv('GEMINI_API_KEY')) {
    $GEMINI_API_KEY = getenv('GEMINI_API_KEY');
}

if (empty($GEMINI_API_KEY)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API key configuration missing',
        'debug' => 'Checked: ' . realpath($envFile) ?: $envFile,
        'exists' => file_exists($envFile)
    ]);
    exit;
}

$GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

$SYSTEM_PROMPT = <<<EOT
You are a support assistant for a freelancing web application called Workify. Your primary goal is to be helpful and provide exact, step-by-step instructions when a user asks how to use a feature. Do NOT give vague or basic replies like "Hello" or "You can do this". Give them actionable steps.

IMPORTANT RULES:
- If a user asks HOW to do something, tell them exactly where to click or go.
- Only talk about features that EXIST in the platform.
- If a feature does NOT exist, say: "This feature is not available yet."
- Do NOT invent features.

AVAILABLE FEATURES AND HOW TO USE THEM:
- Creating publications: Go to the feed or publications page, write your post in the input area, and click the publish button.
- Liking publications: Find the publication you want to like, and click the "Like" button below the post.
- Commenting on publications: Underneath a publication, click on the comment area, type your comment, and submit it.
- Sending messages: Click on "Messages" in the navigation bar, select a user to chat with, type your message, and send it.
- Searching publications: Use the search bar at the top of the publications page to type your keywords and filter posts.

FORBIDDEN:
- Do NOT mention archive.
- Do NOT mention notifications if not implemented.
- Do NOT mention features not listed.

Always be direct, specific, and helpful. Give exact directions.
EOT;

// ── Read incoming JSON body ────────────────────────────────────
$rawBody  = file_get_contents('php://input');
$incoming = json_decode($rawBody, true);

if (!$incoming || empty($incoming['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing "message" field']);
    exit;
}

$userMessage = trim($incoming['message']);

// ── Build Gemini request payload ───────────────────────────────
$payload = json_encode([
    'system_instruction' => [
        'parts' => [
            ['text' => $SYSTEM_PROMPT]
        ]
    ],
    'contents' => [
        [
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ],
    'safetySettings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
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
    echo json_encode(['error' => 'Failed to reach Gemini API: ' . $curlError]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    $geminiData = json_decode($response, true);

    if ($httpCode === 429 || (isset($geminiData['error']['status']) && $geminiData['error']['status'] === 'RESOURCE_EXHAUSTED')) {
        $errorMessage = 'The bot is currently busy. Please wait a minute before trying again.';
    } else {
        $errorMessage = 'Gemini API returned an error';
        if (isset($geminiData['error']['message'])) {
            $errorMessage .= ': ' . $geminiData['error']['message'];
        }
    }

    echo json_encode(['error' => $errorMessage, 'detail' => $geminiData]);
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
