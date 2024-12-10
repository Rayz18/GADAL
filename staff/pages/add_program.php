<?php
include '../../config/config.php';
// Check for form submission and insert the new program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $program_name = $_POST['program_name'];

    // Handle file upload
    $program_img = '';
    if (!empty($_FILES['program_img']['name'])) {
        $target_dir = "../../staff/upload/";
        $target_file = $target_dir . basename($_FILES['program_img']['name']);
        if (move_uploaded_file($_FILES['program_img']['tmp_name'], $target_file)) {
            $program_img = $_FILES['program_img']['name'];  // Store the image filename
        }
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO programs (program_name, program_img) VALUES (?, ?)");
    $stmt->bind_param("ss", $program_name, $program_img);
    $stmt->execute();
    $stmt->close();

    // Redirect to the same page after adding the program
    header('Location: manage_programs.php');  // Refresh page to show the new program
    exit;
}
