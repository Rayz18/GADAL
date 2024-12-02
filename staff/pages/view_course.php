<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Get the course_id from the URL
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    echo "Invalid course ID.";
    exit;
}

// Fetch course details
$course_query = $conn->query("
    SELECT courses.*, programs.program_name 
    FROM courses 
    INNER JOIN programs ON courses.program_id = programs.program_id 
    WHERE courses.course_id = '$course_id' AND courses.archive = TRUE
");
$course = $course_query->fetch_assoc();

if (!$course) {
    echo "Course not found or is not archived.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Course Details</h1>

        <!-- Back Button -->
        <a href="archives.php" class="btn btn-secondary mb-3">Back to Archives</a>

        <!-- Course Details -->
        <div class="card">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h2>
                <p class="card-text"><strong>Program:</strong> <?php echo htmlspecialchars($course['program_name']); ?>
                </p>
                <p class="card-text"><strong>Description:</strong>
                    <?php echo htmlspecialchars($course['course_desc']); ?></p>
                <p class="card-text"><strong>Mode:</strong>
                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $course['offered_mode']))); ?></p>
                <p class="card-text"><strong>Status:</strong> Archived</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>