<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $user_id = $_SESSION['user_id'];
    $hours = mysqli_real_escape_string($conn, $_POST['hours_spent']);

    $sql = "INSERT INTO time_logs (task_id, user_id, hours_spent) VALUES ('$task_id', '$user_id', '$hours')";
    
    if ($conn->query($sql)) {
        header("Location: index.php?success=time_logged");
    }
}
?>