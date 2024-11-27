<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch all programs
$programs_query = $conn->query("SELECT * FROM programs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs and Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../staff/assets/css/manage_programs.css">
    <style>
        .truncate {
            display: inline-block;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        .card-header h5 {
            display: inline-block;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <!-- Include the Staff Navigation Bar -->
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Manage Programs and Courses</h1>

        <div class="text-end mb-3">
            <a href="add_program.php" class="btn btn-success">Add New Program</a>
        </div>

        <!-- Program Tabs -->
        <ul class="nav nav-tabs" id="programTabs" role="tablist">
            <?php if ($programs_query->num_rows > 0) {
                $is_first = true;
                while ($program = $programs_query->fetch_array()) { ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $is_first ? 'active' : ''; ?>"
                            id="tab-<?php echo $program['program_id']; ?>" data-bs-toggle="tab"
                            data-bs-target="#content-<?php echo $program['program_id']; ?>" type="button" role="tab"
                            aria-controls="content-<?php echo $program['program_id']; ?>"
                            aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>">
                            <span class="truncate" title="<?php echo htmlspecialchars($program['program_name']); ?>">
                                <?php echo mb_strimwidth(htmlspecialchars($program['program_name']), 0, 20, '...'); ?>
                            </span>
                        </button>
                    </li>
                    <?php $is_first = false;
                }
            } else { ?>
                <li class="nav-item">
                    <span class="nav-link text-danger">No Programs Available</span>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content" id="programContent">
            <?php
            $programs_query->data_seek(0); // Reset the pointer to reuse the query
            $is_first = true;
            while ($program = $programs_query->fetch_array()) { ?>
                <div class="tab-pane fade <?php echo $is_first ? 'show active' : ''; ?>"
                    id="content-<?php echo $program['program_id']; ?>" role="tabpanel"
                    aria-labelledby="tab-<?php echo $program['program_id']; ?>">
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0" title="<?php echo htmlspecialchars($program['program_name']); ?>">
                                    <?php echo mb_strimwidth(htmlspecialchars($program['program_name']), 0, 25, '...'); ?>
                                </h5>
                                <div>
                                    <a href="edit_program.php?program_id=<?php echo $program['program_id']; ?>"
                                        class="btn btn-light btn-sm me-2">Edit Program</a>
                                    <a href="delete_program.php?program_id=<?php echo $program['program_id']; ?>"
                                        class="btn btn-danger btn-sm">Delete Program</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>Courses in this Program:</h6>

                            <div class="mb-3">
                                <div class="btn-group filter-courses" role="group"
                                    data-program-id="<?php echo $program['program_id']; ?>">
                                    <button type="button" class="btn btn-outline-primary active" data-filter="all">Show
                                        All</button>
                                    <button type="button" class="btn btn-outline-primary"
                                        data-filter="online">Online</button>
                                    <button type="button" class="btn btn-outline-primary"
                                        data-filter="face_to_face">Face-to-Face</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Mode</th>
                                            <th>Actions</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="courses-<?php echo $program['program_id']; ?>">
                                        <?php
                                        $courses_query = $conn->query("
                                            SELECT course_id, course_name, offered_mode, enable_registration, enable_attendance, enable_evaluation 
                                            FROM courses 
                                            WHERE program_id = " . $program['program_id']);
                                        if ($courses_query->num_rows > 0) {
                                            while ($course = $courses_query->fetch_array()) { ?>
                                                <tr data-mode="<?php echo $course['offered_mode']; ?>">
                                                    <td>
                                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars(ucwords($course['offered_mode'])); ?></td>
                                                    <td>
                                                        <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>"
                                                            class="btn btn-primary btn-sm">Edit</a>
                                                        <a href="delete_course.php?course_id=<?php echo $course['course_id']; ?>"
                                                            class="btn btn-danger btn-sm">Delete</a>
                                                    </td>
                                                    <td>
                                                        <?php if ($course['offered_mode'] === 'face_to_face') { ?>
                                                            <button class="btn btn-secondary btn-sm" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#links-<?php echo $course['course_id']; ?>"
                                                                aria-expanded="false"
                                                                aria-controls="links-<?php echo $course['course_id']; ?>">
                                                                Show Links
                                                            </button>
                                                            <div class="collapse mt-2" id="links-<?php echo $course['course_id']; ?>">
                                                                <ul class="list-group list-group-flush">
                                                                    <?php if ($course['enable_registration']) { ?>
                                                                        <li class="list-group-item">
                                                                            <span class="text-muted">Registration:</span>
                                                                            <a href="../../learner/pages/register.php?course_id=<?php echo $course['course_id']; ?>"
                                                                                class="text-info text-decoration-none">
                                                                                Register
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ($course['enable_attendance']) { ?>
                                                                        <li class="list-group-item">
                                                                            <span class="text-muted">Attendance:</span>
                                                                            <a href="../../learner/pages/attendance.php?course_id=<?php echo $course['course_id']; ?>"
                                                                                class="text-info text-decoration-none">
                                                                                Mark Attendance
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ($course['enable_evaluation']) { ?>
                                                                        <li class="list-group-item">
                                                                            <span class="text-muted">Evaluation:</span>
                                                                            <a href="../../learner/pages/evaluation.php?course_id=<?php echo $course['course_id']; ?>"
                                                                                class="text-info text-decoration-none">
                                                                                Evaluate
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        <?php } else if ($course['offered_mode'] === 'online') { ?>
                                                                <a href="add_pre_test.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge bg-info text-white">Pre-Test</a>
                                                                <a href="manage_learning_materials.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge bg-info text-white">Learning Materials</a>
                                                                <a href="add_quiz.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge bg-info text-white">Quiz</a>
                                                                <a href="add_post_test.php?course_id=<?php echo $course['course_id']; ?>"
                                                                    class="badge bg-info text-white">Post-Test</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-danger">No courses available.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="add_course.php?program_id=<?php echo $program['program_id']; ?>"
                                class="btn btn-success btn-sm mt-3">Add New Course</a>
                        </div>
                    </div>
                </div>
                <?php $is_first = false;
            } ?>
        </div>
    </div>

    <script>
        // Filter courses based on the selected mode
        document.querySelectorAll('.filter-courses').forEach(group => {
            const buttons = group.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const programId = group.getAttribute('data-program-id');
                    const selectedMode = this.getAttribute('data-filter');
                    const rows = document.querySelectorAll(`#courses-${programId} tr`);

                    // Highlight the active button
                    buttons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    // Filter the courses
                    rows.forEach(row => {
                        if (selectedMode === 'all' || row.getAttribute('data-mode') === selectedMode) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>