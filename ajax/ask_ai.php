<?php
include '../db.php';
session_start();
header('Content-Type: application/json');


$api_key = getenv("GROQ_API_KEY"); // Make sure your key is here
$my_id = $_SESSION['user_id'];
$ai_id = 99; // AI User ID

$input = json_decode(file_get_contents('php://input'), true);
$question = $input['question'] ?? '';
$project_id = (int)($input['project_id'] ?? 0);

if (!empty($question) && $project_id > 0) {
    // A. SAVE YOUR MESSAGE (So you see it in the chat)
    $stmt_user = $conn->prepare("INSERT INTO messages (project_id, user_id, message_text) VALUES (?, ?, ?)");
    $stmt_user->bind_param("iis", $project_id, $my_id, $question);
    $stmt_user->execute();

    // B. FETCH PROJECT INFO (Giving the AI "eyes")
    $stmt_proj = $conn->prepare("SELECT project_name, description, status FROM projects WHERE id = ?");
    $stmt_proj->bind_param("i", $project_id);
    $stmt_proj->execute();
    $project = $stmt_proj->get_result()->fetch_assoc();

    // C. FETCH TASKS (More detail for the AI)
    $tasks_res = $conn->query("SELECT task_title, status FROM tasks WHERE project_id = $project_id");
    $tasks_list = "";
    while($t = $tasks_res->fetch_assoc()) { $tasks_list .= "- " . $t['task_title'] . " (" . $t['status'] . ")\n"; }

    $context = "Project: " . ($project['project_name'] ?? 'Unknown') . "\n";
    $context .= "Status: " . ($project['status'] ?? 'Ongoing') . "\n";
    $context .= "Tasks:\n" . $tasks_list;

    // D. CALL AI API
    $url = "https://api.groq.com/openai/v1/chat/completions";
    $data = ["model" => "llama-3.3-70b-versatile", "messages" => [
        ["role" => "system", "content" => "You are a helpful project assistant. Context:\n$context"],
        ["role" => "user", "content" => $question]
    ]];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
   $response = curl_exec($ch);
    $curl_error = curl_error($ch); // Catch connection errors
    curl_close($ch);

    if ($curl_error) {
        $ai_text = "Connection Error: " . $curl_error;
    } else {
        $result = json_decode($response, true);
        // This line ensures we get the text or show the specific error from Groq
        $ai_text = $result['choices'][0]['message']['content'] ?? 'API Error: ' . ($result['error']['message'] ?? 'Unknown Error');
    }
   

    // E. SAVE AI RESPONSE
    $stmt_ai = $conn->prepare("INSERT INTO messages (project_id, user_id, message_text) VALUES (?, ?, ?)");
    $stmt_ai->bind_param("iis", $project_id, $ai_id, $ai_text);
    $stmt_ai->execute();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>