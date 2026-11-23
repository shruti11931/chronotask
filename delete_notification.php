<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $notif_id = intval($_GET['id']);

    // Ensure the user can only delete their own notifications
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>