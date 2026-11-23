<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Check if ID is valid
    $query = "DELETE FROM projects WHERE id = '$id'";
    
    if ($conn->query($query)) {
        // Redirect back to the dashboard after successful deletion
        header("Location: index.php?page=dashboard&deleted=1");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    header("Location: index.php?page=dashboard");
    exit();
}
?>