<?php
session_start();
include 'db.php';
include 'mail_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = (int)$_POST['project_id'];
    $task_title = mysqli_real_escape_string($conn, $_POST['task_title']);
    $assigned_to = (int)$_POST['assigned_to']; // Correctly capturing ID 11
    $deadline = $_POST['deadline'];
    
    // Set initial progress to 0 to ensure dynamic calculations work immediately
    $initial_progress = 0;
    $status = 'In Progress'; 

    // 1. Insert into Database with Progress
    $sql = "INSERT INTO tasks (project_id, task_title, assigned_to, deadline, status, progress) 
            VALUES ($project_id, '$task_title', $assigned_to, '$deadline', '$status', $initial_progress)";

    if ($conn->query($sql)) {
        // 2. Fetch the Team Member's Email
        $user_query = $conn->query("SELECT email, full_name FROM users WHERE id = $assigned_to");
        $user = $user_query->fetch_assoc();
        
        if ($user) {
            $to_email = $user['email'];
            $subject = "🚀 New Task: " . $task_title;
            $body = "
                <div style='font-family: sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #334155;'>Hello " . htmlspecialchars($user['full_name']) . ",</h2>
                    <p>You have a new assignment in <b>ProManage</b>.</p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p><b>Task:</b> " . htmlspecialchars($task_title) . "</p>
                    <p><b>Deadline:</b> " . date('M d, Y', strtotime($deadline)) . "</p>
                    <a href='http://localhost/promage/index.php?page=tasks' 
                       style='display: inline-block; padding: 10px 20px; background: #0f172a; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                       View My Tasks
                    </a>
                </div>
            ";

            sendSmartEmail($to_email, $subject, $body);
        }

        header("Location: index.php?page=project_details&id=$project_id&msg=success");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>