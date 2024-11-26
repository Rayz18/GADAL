<?php
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    header('Location: manage_programs.php');  // Redirect to the manage programs page after adding a program
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Program</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <form action="add_program.php" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm"
        style="max-width: 500px; width: 100%;">
        <h2 class="text-center text-primary mb-4">Add Program</h2>

        <div class="mb-3">
            <label for="program_name" class="form-label fw-bold">Program Name:</label>
            <input type="text" id="program_name" name="program_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="program_img" class="form-label fw-bold">Program Image:</label>
            <input type="file" id="program_img" name="program_img" class="form-control">
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">Add Program</button>
            <a href="manage_programs.php" class="btn btn-secondary">Back</a>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>