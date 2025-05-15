<?php
// ai_recommendation.php
header('Content-Type: application/json');
require_once 'db.php';
session_start();

$student_id = isset($_SESSION['parent_email']) ? ($conn->query("SELECT student_id, classes FROM parents_students WHERE email='" . $_SESSION['parent_email'] . "'")->fetch_assoc()['student_id'] ?? null) : null;
$grade = isset($_SESSION['parent_email']) ? ($conn->query("SELECT classes FROM parents_students WHERE email='" . $_SESSION['parent_email'] . "'")->fetch_assoc()['classes'] ?? null) : null;

if (!$student_id) {
    echo json_encode(['error' => 'Not logged in or student not found.']);
    exit;
}

// Aggregate sums from report_history
$sum_participation = 0;
$sum_understanding = 0;
$sum_behavior = 0;
$sum_emotional = 0;
$notes = [];

$result = $conn->query("SELECT participation, understanding, behavior, emotional, notes FROM report_history WHERE student_id=$student_id");
while ($row = $result->fetch_assoc()) {
    $sum_participation += intval($row['participation']);
    $sum_understanding += intval($row['understanding']);
    $sum_behavior += intval($row['behavior']);
    $sum_emotional += intval($row['emotional']);
    if (!empty($row['notes'])) $notes[] = $row['notes'];
};

// Construct prompt for the AI API
$prompt = "Hey, so I am a student in grade $grade and I have a sum of reports of $sum_participation in participation, $sum_understanding in understanding, $sum_behavior in behavior, $sum_emotional in emotional. Could you please tell me, what does it say about me? And how can I improve myself? My teacher also left me these notes: " . implode(", ", $notes) . ". Could you also give me a link to a YouTube video that could help me improve myself, and one that aligns with what I might like? Please return your answer as JSON with ai_notes, youtube_link_to_improve, youtube_link_for_improving.";

// Call OpenAI API (or similar) - placeholder API_KEY
$api_key = 'YOUR_OPENAI_API_KEY'; // <-- Replace with your key
$api_url = 'https://api.openai.com/v1/chat/completions';

$payload = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'You are an educational AI assistant.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'max_tokens' => 512,
    'temperature' => 0.7
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && $response) {
    $data = json_decode($response, true);
    // Expecting the AI to return a JSON string as its answer
    $ai_json = null;
    if (isset($data['choices'][0]['message']['content'])) {
        $ai_json = json_decode($data['choices'][0]['message']['content'], true);
    }
    if ($ai_json && is_array($ai_json)) {
        echo json_encode($ai_json);
        exit;
    } else {
        echo json_encode(['error' => 'AI did not return valid JSON. Raw: ' . $data['choices'][0]['message']['content']]);
        exit;
    }
}

// Fallback error
http_response_code(500);
echo json_encode(['error' => 'Failed to get AI recommendation.']);
exit;
