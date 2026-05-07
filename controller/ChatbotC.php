<?php
header('Content-Type: application/json');

// --- CONFIGURATION SANS CLE API (Pollinations AI) ---
// Cette API est 100% gratuite, n'a pas besoin de clé API, et ne bloque pas les requêtes.

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? $input['message'] : '';

if (!$userMessage) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$data = [
    'model' => 'openai', // Pollinations gère automatiquement le meilleur modèle
    'messages' => [
        [
            'role' => 'system',
            'content' => "You are an expert assistant for the Workify platform, specifically the training management module (Gestion des Formations). You MUST ONLY answer questions related to this website, its training programs, courses, scheduling, categories, and learning. If the user asks about anything unrelated to this website or its domain, you MUST politely refuse to answer and state that you can only assist with website-related topics. Be concise, helpful, and speak in the language the user speaks."
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
    'User-Agent: Workify-Chatbot/1.0' // Requis par Pollinations
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
