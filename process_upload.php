<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $cat = $_POST['category'];
    
    // FIX: We must provide a valid project_id
    // If you don't have a project selection yet, we'll fetch the first available ID
    $project_res = $conn->query("SELECT id FROM projects LIMIT 1");
    $project_data = $project_res->fetch_assoc();
    $project_id = $project_data['id']; 

    if (!$project_id) {
        die("Error: You must create a project before uploading resources.");
    }

    $final_path = $link;
    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_name = time() . "_" . basename($_FILES["resource_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["resource_file"]["tmp_name"], $target_file)) {
            $final_path = $target_file;
        }
    }

    // UPDATED QUERY: Including project_id to satisfy the foreign key
    $sql = "INSERT INTO tasks (project_id, task_title, resource_links, resource_category, uploaded_by, status) 
            VALUES ('$project_id', '$title', '$final_path', '$cat', 'Admin', 'Resource')";

    if ($conn->query($sql)) {
        header("Location: index.php?page=resource&success=1");
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>