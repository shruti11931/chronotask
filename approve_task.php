<?php
include 'db.php';
session_start();

$task_id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'approve') {
    $status = 'Completed';
    $msg = "Your task has been approved by the mentor!";
} else {
    $status = 'Rejected';
    $msg = "Task rejected. Please check mentor feedback and try again.";
}

$conn->query("UPDATE tasks SET status = '$status' WHERE id = $task_id");

// Notify the user
$task_info = $conn->query("SELECT assigned_to FROM tasks WHERE id = $task_id")->fetch_assoc();

// Corrected notification insert
$notif_sql = "INSERT INTO notifications (user_id, message, type, is_read) 
              VALUES ({$task_info['assigned_to']}, '$msg', 'approval', 0)";
$conn->query($notif_sql);
header("Location: index.php?page=mentor_dashboard");