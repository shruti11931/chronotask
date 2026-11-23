<?php
include 'db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
    $task_id = (int)$_POST['task_id'];
    $new_status = $_POST['status']; 
    
    // 1. Update the Task status
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $task_id);
    
    if ($stmt->execute()) {
        // 2. Get the Project ID to update project progress bar
        $res = $conn->query("SELECT project_id FROM tasks WHERE id = $task_id");
        $project_data = $res->fetch_assoc();
        $pid = $project_data['project_id'];

        // 3. Recalculate Project Progress based on completed tasks
        $total_res = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE project_id = $pid");
        $done_res = $conn->query("SELECT COUNT(*) as done FROM tasks WHERE project_id = $pid AND status = 'Completed'");
        
        $total = $total_res->fetch_assoc()['total'];
        $done = $done_res->fetch_assoc()['done'];
        
        $new_avg = ($total > 0) ? round(($done / $total) * 100) : 0;

        // 4. Save new progress to the projects table
        $conn->query("UPDATE projects SET progress = $new_avg WHERE id = $pid");

        header("Location: index.php?page=tasks&msg=updated");
    } else {
        header("Location: index.php?page=tasks&error=failed");
    }
    exit();
}