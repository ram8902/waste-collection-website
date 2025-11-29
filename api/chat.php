<?php
// Prevent PHP warnings from breaking JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();
set_time_limit(120); // Allow script to run longer for fallbacks

// Configuration
$OPENROUTER_API_KEY = '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'; 
// List of models to try in order
$MODELS = [
    'deepseek/deepseek-chat:free',
    'google/gemma-2-9b-it:free',
    'meta-llama/llama-3.2-3b-instruct:free',
    'microsoft/phi-3-mini-128k-instruct:free',
    'mistralai/mistral-7b-instruct:free'
];

$SITE_URL = 'http://localhost/garbage_project';
$SITE_NAME = 'Waste Collection Service';

// Get the input
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

if (empty($message)) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

// Check if API token is set
if ($OPENROUTER_API_KEY === 'sk-or-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' || empty($OPENROUTER_API_KEY)) {
    $mock_responses = [
        "I'm a demo bot! Please add an OpenRouter API key to api/chat.php to make me smart.",
        "I can help you book a pickup or track your waste status.",
        "To book a pickup, click on 'Book Pickup' in the navigation menu.",
        "You can track your request status using the 'Track Status' page."
    ];
    echo json_encode(['response' => $mock_responses[array_rand($mock_responses)]]);
    exit;
}

// Prepare the messages for OpenRouter
$system_prompt = "You are a helpful assistant for a Waste Collection Service. 
Your goal is to help users with booking pickups, tracking status, and answering questions about waste management.
Keep your answers concise and friendly.
Context:
- Users can book pickups via the 'Book Pickup' page.
- Users can track status via the 'Track Status' page.
- Users can view history via the 'My History' page.
- We collect plastic, e-waste, metal, and household waste.";

$messages = [
    [
        "role" => "system",
        "content" => $system_prompt
    ],
    [
        "role" => "user",
        "content" => $message
    ]
];

$last_error = '';
$success = false;
$generated_text = '';

// OpenRouter API URL
$url = "https://openrouter.ai/api/v1/chat/completions";

foreach ($MODELS as $model) {
    // Log attempt
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Trying model: $model\n", FILE_APPEND);

    $data = [
        'model' => $model,
        'messages' => $messages
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $OPENROUTER_API_KEY,
        'HTTP-Referer: ' . $SITE_URL,
        'X-Title: ' . $SITE_NAME,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set a longer timeout for free models which can be slow
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    // Disable SSL verification for local development (fixes common Windows PHP issues)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($http_code === 200) {
        $result = json_decode($response, true);
        $content = $result['choices'][0]['message']['content'] ?? '';
        
        if (!empty(trim($content))) {
            $generated_text = str_replace(['<s>', '</s>'], '', $content);
            $success = true;
            file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Success with $model\n", FILE_APPEND);
            break; // Exit loop on success
        }
    }
    
    // Log failure and continue to next model
    $error_msg = $curl_error ? "cURL Error: $curl_error" : ($response ? $response : 'No response');
    $last_error = "Model $model failed ($http_code): $error_msg";
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - $last_error\n", FILE_APPEND);
}

if ($success) {
    echo json_encode(['response' => trim($generated_text)]);
} else {
    echo json_encode(['response' => "Sorry, I'm having trouble connecting. All my brain cells are busy! (Last Error: $last_error)"]);
}
?>
