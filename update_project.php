<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $manager = mysqli_real_escape_string($conn, $_POST['manager_name']);
    $date = $_POST['due_date'];
    $status = $_POST['status'];
    $progress = (int)$_POST['progress'];

    $sql = "UPDATE projects SET 
            project_name='$name', 
            manager_name='$manager', 
            due_date='$date', 
            start_date = '$start', 
            deadline = '$end',
            status='$status', 
            progress=$progress 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    }
}
?>