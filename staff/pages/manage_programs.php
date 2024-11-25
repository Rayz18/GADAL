<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch all programs, with the most recent one first
$programs_query = $conn->query("SELECT * FROM programs ORDER BY created_at DESC");

// Function to truncate program names
function truncateProgramName($name, $limit = 15)
{
    $words = explode(' ', $name);
    if (count($words) > $limit) {
        $name = implode(' ', array_slice($words, 0, $limit)) . '<br>' . implode(' ', array_slice($words, $limit));
    }
    return $name;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs and Courses</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/manage_programs.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Include the Staff Navigation Bar -->
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <div class="layout">
        <!-- Sidebar -->
        <div class="sidebar collapsed" id="sidebar"></div>

        <!-- Page Content -->
        <div id="content" class="content">
            <div id="toggle-sidebar" class="toggle-sidebar"></div>

            <div class="container my-5">
                <h1 class="text-center text-white py-3 custom-header">Manage Programs and Courses</h1>

                <div class="text-right mb-4">
                    <a href="add_program.php" class="btn btn-success">Add New Program</a>
                </div>

                <!-- Accordion for Programs -->
                <div class="accordion" id="programAccordion">
                    <?php if ($programs_query->num_rows > 0) {
                        while ($program = $programs_query->fetch_array()) { ?>
                            <div class="card mb-3 program-card">
                                <div class="card-header d-flex justify-content-between align-items-center <?php echo isset($_GET['program_id']) && $_GET['program_id'] == $program['program_id'] ? 'active' : ''; ?>"
                                    id="heading-<?php echo $program['program_id']; ?>">
                                    <h5 class="mb-0 program-title">
                                        <button class="btn btn-link toggle-link" type="button" data-toggle="collapse"
                                            data-target="#collapse-<?php echo $program['program_id']; ?>" aria-expanded="false"
                                            aria-controls="collapse-<?php echo $program['program_id']; ?>">
                                            <?php echo truncateProgramName(htmlspecialchars($program['program_name']), 15); ?>
                                        </button>
                                    </h5>
                                    <div class="d-flex program-buttons">
                                        <?php if (isset($_GET['program_id']) && $_GET['program_id'] == $program['program_id']) { ?>
                                            <span class="badge badge-primary">Selected</span>
                                        <?php } ?>
                                        <a href="edit_program.php?program_id=<?php echo $program['program_id']; ?>"
                                            class="btn btn-primary btn-sm mr-2">Edit</a>
                                        <a href="delete_program.php?program_id=<?php echo $program['program_id']; ?>"
                                            class="btn btn-danger btn-sm">Delete</a>
                                    </div>
                                </div>
                                <div id="collapse-<?php echo $program['program_id']; ?>"
                                    class="collapse <?php echo isset($_GET['program_id']) && $_GET['program_id'] == $program['program_id'] ? 'show' : ''; ?>"
                                    aria-labelledby="heading-<?php echo $program['program_id']; ?>"
                                    data-parent="#programAccordion">
                                    <div class="card-body">
                                        <p class="program-desc"><?php echo nl2br(htmlspecialchars($program['program_desc'])); ?>
                                        </p>

                                        <h6>Courses under this program:</h6>
                                        <div class="course-list">
                                            <?php
                                            $courses_query = $conn->query("
                                                SELECT course_id, course_name, offered_mode, enable_registration, enable_attendance, enable_evaluation 
                                                FROM courses 
                                                WHERE program_id = " . $program['program_id']);
                                            if ($courses_query->num_rows > 0) {
                                                while ($course = $courses_query->fetch_array()) {
                                                    $offered_mode = $course['offered_mode'];
                                                    $enable_registration = $course['enable_registration'];
                                                    $enable_attendance = $course['enable_attendance'];
                                                    $enable_evaluation = $course['enable_evaluation'];
                                                    ?>
                                                    <div class="course-item mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="course-name text-primary font-weight-bold">
                                                                <?php echo htmlspecialchars($course['course_name']); ?>
                                                            </span>
                                                            <div class="course-actions">
                                                                <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="btn btn-primary btn-sm">Edit</a>
                                                                <a href="delete_course.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="btn btn-danger btn-sm">Delete</a>
                                                            </div>
                                                        </div>
                                                        <div class="course-options mt-2">
                                                            <?php if ($offered_mode == 'face_to_face'): ?>
                                                                <?php if ($enable_registration): ?>
                                                                    <a href="add_registration.php?course_id=<?php echo $course['course_id']; ?>"
                                                                        class="badge badge-info">Registration</a>
                                                                <?php endif; ?>
                                                                <?php if ($enable_attendance): ?>
                                                                    <a href="add_attendance.php?course_id=<?php echo $course['course_id']; ?>"
                                                                        class="badge badge-info">Attendance</a>
                                                                <?php endif; ?>
                                                                <?php if ($enable_evaluation): ?>
                                                                    <a href="add_evaluation.php?course_id=<?php echo $course['course_id']; ?>"
                                                                        class="badge badge-info">Evaluation</a>
                                                                <?php endif; ?>
                                                            <?php elseif ($offered_mode == 'online'): ?>
                                                                <a href="add_introduction.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge badge-info">Introduction</a>
                                                                <a href="add_pre_test.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge badge-info">Pre-Test</a>
                                                                <a href="add_learning_materials.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge badge-info">Learning Materials</a>
                                                                <a href="add_videos.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge badge-info">Videos</a>
                                                                <a href="add_post_test.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge badge-info">Post-Test</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php }
                                            } else { ?>
                                                <p class="text-danger">No courses available for this program.</p>
                                            <?php } ?>
                                        </div>
                                        <a href="add_course.php?program_id=<?php echo $program['program_id']; ?>"
                                            class="btn btn-success btn-sm mt-3">Add New Course</a>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="text-center text-danger">
                            <p>No programs available. Add a new program to get started!</p>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const sidebar = document.getElementById("sidebar");
                    const content = document.getElementById("content");
                    const toggleButton = document.getElementById("toggle-sidebar");

                    toggleButton.addEventListener("click", function () {
                        sidebar.classList.toggle("collapsed");
                        content.classList.toggle("shifted");
                    });
                });
            </script>
</body>

</html>