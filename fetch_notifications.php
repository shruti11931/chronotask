<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit('0');
}

$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = $user_id AND is_read = 0");
$row = $query->fetch_assoc();

echo $row['count'] ?? 0;
?>