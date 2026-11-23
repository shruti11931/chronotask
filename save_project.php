<?php
session_start();
include 'db.php';
include 'mail_helper.php'; // 1. Include the mailer engine you created

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $manager = mysqli_real_escape_string($conn, $_POST['manager_name']);
    $status = $_POST['status'];
    $progress = (int)$_POST['progress'];
    $start = $_POST['start_date'];
    $end = $_POST['deadline'];
    $due = $_POST['due_date'];

    $sql = "INSERT INTO projects (project_name, manager_name, status, progress, start_date, deadline, due_date) 
            VALUES ('$name', '$manager', '$status', $progress, '$start', '$end', '$due')";

    if ($conn->query($sql) === TRUE) {
        $project_id = $conn->insert_id; // Get the ID of the new project

        // 2. Fetch the Manager's email from the users table
        // Assumes manager_name in projects matches full_name in users
        $user_query = $conn->query("SELECT email FROM users WHERE full_name = '$manager' LIMIT 1");
        
        if ($user_query && $user_query->num_rows > 0) {
            $user_data = $user_query->fetch_assoc();
            $to_email = $user_data['email'];

            // 3. Send the Smart Notification Email
            $subject = "New Project Assigned: $name";
            $message = "Hello $manager,<br><br>You have been assigned as the manager for the new project: <b>$name</b>.<br>
                        Start Date: $start<br>
                        Deadline: $end<br><br>
                        Please log in to ProManage to view the roadmap.";
            
            sendSmartEmail($to_email, $subject, $message);
        }

        // 4. Also insert a dashboard notification
        $notif_msg = "You have been assigned to project: $name";
        $conn->query("INSERT INTO notifications (user_id, project_id, message, type) 
                      SELECT id, $project_id, '$notif_msg', 'task_assigned' 
                      FROM users WHERE full_name = '$manager'");

        header("Location: index.php?page=timeline");
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
}
?>