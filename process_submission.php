<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = (int)$_POST['task_id'];
    $project_id = (int)$_POST['project_id'];
    
    // File Upload Logic
    $target_dir = "uploads/submissions/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . basename($_FILES["work_file"]["name"]);
    $target_file = $target_dir . $file_name;

    // ... (after the task update query)

// Get the Mentor ID correctly
$project_res = $conn->query("SELECT mentor_id FROM projects WHERE id = $project_id");
$project_data = $project_res->fetch_assoc();
$mentor_id = $project_data['mentor_id'] ?? 0;

if ($mentor_id > 0) {
    // The error was likely here: missing the column names (user_id, message)
    $notif_msg = "New submission received for Task #" . $task_id;
    $notif_sql = "INSERT INTO notifications (user_id, message, is_read) VALUES ($mentor_id, '$notif_msg', 0)";
    
    if (!$conn->query($notif_sql)) {
        // This helps you see the error if it fails again
        die("Notification Error: " . $conn->error);
    }
}

// Redirect back
header("Location: index.php?page=project_details&id=$project_id");
}