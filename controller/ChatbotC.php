<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim((string) $input['message']) : '';
$module = isset($input['module']) ? preg_replace('/[^a-z]/i', '', (string) $input['module']) : 'formations';

if ($userMessage === '') {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$prompts = [
    'formations' => 'You are the Workify training assistant. Help only with formations, learning plans, training registration, course categories, schedules and trainer questions. If the question is outside Workify or training, politely redirect to Workify training topics. Reply in the user language and stay concise.',
    'publications' => 'You are the Workify publication assistant. Help users write, improve, summarize and structure community posts, opportunities, announcements and comments for the Workify feed. If the question is outside Workify publication/community work, politely redirect to feed-related help. Reply in the user language and stay concise.',
    'messages' => 'You are the Workify messaging assistant. Help users draft professional messages, replies, follow-ups and collaboration notes for Workify conversations. If the question is outside Workify messaging/collaboration, politely redirect to message-related help. Reply in the user language and stay concise.'
];

if (!isset($prompts[$module])) {
    $module = 'formations';
}

$data = [
    'model' => 'openai',
    'messages' => [
        [
            'role' => 'system',
            'content' => $prompts[$module]
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ],
    'temperature' => 0.7
];

$ch = curl_init('https://text.pollinations.ai/openai');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: Workify-Chatbot/1.0'
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    $decodedResponse = json_decode($response, true);
    if (isset($decodedResponse['error'])) {
        $errorMessage = is_array($decodedResponse['error']) && isset($decodedResponse['error']['message'])
            ? $decodedResponse['error']['message']
            : json_encode($decodedResponse['error']);
        echo json_encode(['error' => $errorMessage]);
    } else {
        echo $response;
    }
}

curl_close($ch);
?>
