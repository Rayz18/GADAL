<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Fetch archived programs
$archived_programs_query = $conn->query("SELECT * FROM programs WHERE archive = TRUE");

// Fetch archived courses
$archived_courses_query = $conn->query("
    SELECT courses.*, programs.program_name 
    FROM courses 
    INNER JOIN programs ON courses.program_id = programs.program_id 
    WHERE courses.archive = TRUE
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Programs and Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <!-- Page Title -->
        <h1 class="text-center mb-4">Archived Programs and Courses</h1>

        <!-- Back Button -->
        <div class="text-end">
            <a href="manage_programs.php" class="btn btn-secondary mb-3">Back</a>
        </div>

        <!-- Tabs for Archived Programs and Courses -->
        <ul class="nav nav-tabs" id="archivesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="programs-tab" data-bs-toggle="tab"
                    data-bs-target="#archivedPrograms" type="button" role="tab" aria-controls="archivedPrograms"
                    aria-selected="true">
                    Archived Programs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#archivedCourses"
                    type="button" role="tab" aria-controls="archivedCourses" aria-selected="false">
                    Archived Courses
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="archivesTabContent">
            <!-- Archived Programs -->
            <div class="tab-pane fade show active" id="archivedPrograms" role="tabpanel" aria-labelledby="programs-tab">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Program Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($archived_programs_query->num_rows > 0) {
                                while ($program = $archived_programs_query->fetch_array()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($program['program_name']); ?></td>
                                        <td class="text-center">
                                            <a href="view_program.php?program_id=<?php echo $program['program_id']; ?>"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="restore_handler.php?program_id=<?php echo $program['program_id']; ?>"
                                                class="btn btn-warning btn-sm">Restore</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="2" class="text-center text-danger">No archived programs available.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Archived Courses -->
            <div class="tab-pane fade" id="archivedCourses" role="tabpanel" aria-labelledby="courses-tab">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Course Name</th>
                                <th>Program</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($archived_courses_query->num_rows > 0) {
                                while ($course = $archived_courses_query->fetch_array()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['program_name']); ?></td>
                                        <td class="text-center">
                                            <a href="view_course.php?course_id=<?php echo $course['course_id']; ?>"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="restore_handler.php?course_id=<?php echo $course['course_id']; ?>"
                                                class="btn btn-warning btn-sm">Restore</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="3" class="text-center text-danger">No archived courses available.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>