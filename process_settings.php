<?php
include 'db.php';

// SAVE PROJECT SETTINGS (Fixes Line 33 error)
if (isset($_POST['save_project'])) {
    $pid = $_POST['project_id'];
    $name = mysqli_real_escape_string($conn, $_POST['proj_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['proj_desc']);
    $deadline = $_POST['deadline'];
    
    // Correct query using 'name' column
    $sql = "UPDATE projects SET name='$name', description='$desc', deadline='$deadline' WHERE id='$pid'";
    
    if($conn->query($sql)) {
        header("Location: index.php?page=settings&status=project_updated");
    } else {
        echo "Error: " . $conn->error;
    }
    exit();
}

// SAVE PROFILE
if (isset($_POST['save_profile'])) {
    $uid = 1; // Replace with session
    $user = $_POST['username'];
    $skills = $_POST['skills'];
    $bio = $_POST['bio'];
    
    $conn->query("UPDATE users SET username='$user', skills='$skills', bio='$bio' WHERE id=$uid");
    
    // Handle Image
    if (!empty($_FILES['profile_img']['name'])) {
        $img = time() . "_" . $_FILES['profile_img']['name'];
        move_uploaded_file($_FILES['profile_img']['tmp_name'], "uploads/profiles/" . $img);
        $conn->query("UPDATE users SET profile_pix='$img' WHERE id=$uid");
    }
    
    header("Location: index.php?page=settings&status=profile_updated");
    exit();
}
?>