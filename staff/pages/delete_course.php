<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Check if course ID is provided
if (!isset($_GET['course_id'])) {
    die('Course ID is required!');
}

$course_id = intval($_GET['course_id']);

// Fetch course details
$course_query = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();

if ($course_result->num_rows === 0) {
    die('Course not found!');
}

$course = $course_result->fetch_assoc();

// Handle deletion confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // Delete the course
        $delete_query = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $delete_query->bind_param("i", $course_id);

        if ($delete_query->execute()) {
            header('Location: manage_programs.php?message=Course deleted successfully!');
            exit;
        } else {
            $error_message = "Failed to delete the course. Please try again.";
        }
    } else {
        // Redirect back if deletion is canceled
        header('Location: manage_programs.php?message=Deletion canceled.');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Course</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/delete_course.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center text-danger">Delete Course</h1>
        <div class="card p-4">
            <h5>Are you sure you want to delete the following course?</h5>
            <p><strong>Course Name:</strong> <?php echo htmlspecialchars($course['course_name']); ?></p>
            <p><strong>Course Description:</strong> <?php echo nl2br(htmlspecialchars($course['course_desc'])); ?></p>

            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="text-center">
                    <button type="submit" name="confirm" value="yes" class="btn btn-danger">Yes, Delete</button>
                    <a href="manage_programs.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>