<?php
include '../db.php';
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

if (!$user_id) exit;

$duration_hours = $data['duration'] / 3600; // Convert seconds to decimal hours

// Check if a log already exists for this user today
$check = $conn->query("SELECT id FROM time_logs WHERE user_id = $user_id AND DATE(login_time) = CURDATE() LIMIT 1");

if ($check->num_rows > 0) {
    // Update existing log with cumulative time
    $log_id = $check->fetch_assoc()['id'];
    $stmt = $conn->prepare("UPDATE time_logs SET hours_spent = ?, logout_time = NOW() WHERE id = ?");
    $stmt->bind_param("di", $duration_hours, $log_id);
    $stmt->execute();
} else {
    // Create new log for the start of the session
    $stmt = $conn->prepare("INSERT INTO time_logs (user_id, hours_spent, login_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("id", $user_id, $duration_hours);
    $stmt->execute();
}
?>