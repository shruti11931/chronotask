<?php
include 'db.php';
session_start();

if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];
    $project_id = (int)$_GET['project_id'];

    // 1. Update status to 'Pending Approval'
    $sql = "UPDATE tasks SET status = 'Pending Approval' WHERE id = $task_id";
    
    if ($conn->query($sql)) {
        // 2. Notify the Mentor (Optional but recommended for Real-Time)
        // Find who the mentor is for this project
        $project = $conn->query("SELECT mentor_id FROM projects WHERE id = $project_id")->fetch_assoc();
        $mentor_id = $project['mentor_id'];
        
        $msg = "A team member has submitted a task for your approval.";
        $conn->query("INSERT INTO notifications (user_id, message, type) VALUES ($mentor_id, '$msg', 'submission')");

        // 3. Go back
        header("Location: index.php?page=project_details&id=$project_id");
    }
}
?>