<?php
session_start();
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $project_id = $_POST['project_id'];
    $task_id = $_POST['task_id']; // New variable
    $hours = $_POST['hours_spent'];
    $desc = mysqli_real_escape_string($conn, $_POST['task_description']);
    $date = date('Y-m-d');

    // Add task_id to your INSERT statement
    $sql = "INSERT INTO time_logs (project_id, user_id, task_id, hours_spent, task_description, log_date) 
            VALUES ('$project_id', '$user_id', '$task_id', '$hours', '$desc', '$date')";

    if ($conn->query($sql)) {
        header("Location: index.php?page=timelog");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>