<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Get course ID from query string
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

// Update course details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_desc = trim($_POST['course_desc']);

    if (empty($course_name)) {
        $error_message = "Course name cannot be empty.";
    } else {
        $update_query = $conn->prepare("UPDATE courses SET course_name = ?, course_desc = ? WHERE course_id = ?");
        $update_query->bind_param("ssi", $course_name, $course_desc, $course_id);

        if ($update_query->execute()) {
            $success_message = "Course updated successfully!";
            // Refresh course details
            $course['course_name'] = $course_name;
            $course['course_desc'] = $course_desc;
        } else {
            $error_message = "Failed to update course. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/edit_course.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Edit Course</h1>
        <a href="manage_programs.php" class="btn btn-secondary mb-3">Back to Programs</a>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>

        <form method="POST" class="p-3 border rounded">
            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" id="course_name" class="form-control"
                    value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="course_desc">Course Description</label>
                <textarea name="course_desc" id="course_desc" rows="5"
                    class="form-control"><?php echo htmlspecialchars($course['course_desc']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Course</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>