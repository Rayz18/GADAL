<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Check if the program ID is provided
if (isset($_GET['program_id'])) {
    $program_id = intval($_GET['program_id']);

    // Fetch the program details to confirm deletion
    $program_query = $conn->prepare("SELECT program_name FROM programs WHERE program_id = ?");
    $program_query->bind_param("i", $program_id);
    $program_query->execute();
    $result = $program_query->get_result();

    if ($result->num_rows > 0) {
        $program = $result->fetch_assoc();

        // If the form is submitted for deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            // Delete all courses under the program
            $delete_courses_query = $conn->prepare("DELETE FROM courses WHERE program_id = ?");
            $delete_courses_query->bind_param("i", $program_id);
            $delete_courses_query->execute();

            // Delete the program
            $delete_program_query = $conn->prepare("DELETE FROM programs WHERE program_id = ?");
            $delete_program_query->bind_param("i", $program_id);
            $delete_program_query->execute();

            // Redirect to manage_programs.php after deletion
            header('Location: manage_programs.php?message=Program+deleted+successfully');
            exit;
        }
    } else {
        // If program doesn't exist
        header('Location: manage_programs.php?error=Program+not+found');
        exit;
    }
} else {
    // If program ID is not provided
    header('Location: manage_programs.php?error=Invalid+request');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Program</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Delete Program</h5>
                <p class="card-text">Are you sure you want to delete the program
                    <strong><?php echo htmlspecialchars($program['program_name']); ?></strong>?
                </p>
                <p class="text-danger">This action cannot be undone and will also delete all associated courses.</p>
                <form method="POST">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Delete</button>
                    <a href="manage_programs.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>