<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Get the program_id from the URL
$program_id = $_GET['program_id'] ?? null;

if (!$program_id) {
    echo "Invalid program ID.";
    exit;
}

// Fetch program details
$program_query = $conn->query("SELECT * FROM programs WHERE program_id = '$program_id' AND archive = TRUE");
$program = $program_query->fetch_assoc();

if (!$program) {
    echo "Program not found or is not archived.";
    exit;
}

// Fetch associated courses
$courses_query = $conn->query("SELECT * FROM courses WHERE program_id = '$program_id' AND archive = TRUE");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Program Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Program Details</h1>

        <!-- Back Button -->
        <a href="archives.php" class="btn btn-secondary mb-3">Back to Archives</a>

        <!-- Program Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h2>
                <p class="card-text"><strong>Status:</strong> Archived</p>
            </div>
        </div>

        <!-- Associated Courses -->
        <h3>Archived Courses in this Program</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Mode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($courses_query->num_rows > 0) {
                        while ($course = $courses_query->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_desc']); ?></td>
                                <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $course['offered_mode']))); ?>
                                </td>
                                <td>
                                    <a href="view_course.php?course_id=<?php echo $course['course_id']; ?>"
                                        class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="4" class="text-center text-danger">No archived courses found in this program.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>