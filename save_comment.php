<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $project_id = (int)$_POST['project_id'];
    $user_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $sql = "INSERT INTO project_comments (project_id, user_id, message) 
            VALUES ($project_id, $user_id, '$message')";

    if ($conn->query($sql)) {
        echo "Success"; // Just tell JS it worked
    }
}
?>