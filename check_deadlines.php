<?php
include_once 'db.php';
include_once 'mail_helper.php';

// 1. Get dates
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

try {
    // --- TASK REMINDERS (DUE TOMORROW) ---
    // Using 'assigned_to' and 'task_title' from your database screenshot
    $upcoming_query = "SELECT t.task_title, u.email, u.id as user_id 
                       FROM tasks t 
                       JOIN users u ON t.assigned_to = u.id 
                       WHERE DATE(t.deadline) = '$tomorrow' 
                       AND t.status != 'Completed'";

    $upcoming = $conn->query($upcoming_query);

    if ($upcoming && $upcoming->num_rows > 0) {
        while($task = $upcoming->fetch_assoc()) {
            $msg = "Reminder: Your task '" . $task['task_title'] . "' is due tomorrow.";
            
            // Send real email
            sendSmartEmail($task['email'], "Deadline Approaching", $msg);
            
            // Save to dashboard notifications table
            $conn->query("INSERT INTO notifications (user_id, message, type) 
                          VALUES ({$task['user_id']}, '$msg', 'deadline_approaching')");
        }
    }

    // --- OVERDUE WARNINGS (DUE BEFORE TODAY) ---
    $overdue_query = "SELECT t.task_title, u.email, u.id as user_id 
                      FROM tasks t 
                      JOIN users u ON t.assigned_to = u.id 
                      WHERE DATE(t.deadline) < '$today' 
                      AND t.status != 'Completed'";

    $overdue = $conn->query($overdue_query);

    if ($overdue && $overdue->num_rows > 0) {
        while($task = $overdue->fetch_assoc()) {
            $msg = "ALERT: Task '" . $task['task_title'] . "' is now OVERDUE!";
            
            sendSmartEmail($task['email'], "Overdue Warning", $msg);
            
            $conn->query("INSERT INTO notifications (user_id, message, type) 
                          VALUES ({$task['user_id']}, '$msg', 'overdue')");
        }
    }

} catch (mysqli_sql_exception $e) {
    // This prevents the "Fatal Error" from breaking your site
    error_log("Notification Error: " . $e->getMessage());
}
?>