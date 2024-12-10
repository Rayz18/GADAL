<?php
session_start(); // Start session before any output
require_once "../../config/config.php";

// Check if learner is logged in
if (!isset($_SESSION['learner_id'])) {
    header('Location: login.php');
    exit;
}

// Get learner ID
$learner_id = $_SESSION['learner_id'];

// Get program_id from URL parameters
$program_id = $_GET['program_id'];

// Fetch approved program details using the program_id
$program_query = $conn->query("SELECT * FROM programs WHERE program_id = '$program_id' AND status = 'approved'");
$program = $program_query->fetch_assoc();

if (!$program) {
    echo "Program not found or not approved.";
    exit;
}

// Handle enroll/unenroll actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $offered_mode = $_POST['offered_mode']; // Fetch offered_mode from form input

    if (isset($_POST['enroll'])) {
        // Enroll the learner in the course
        $conn->query("INSERT INTO enrollments (learner_id, course_id) VALUES ('$learner_id', '$course_id')");

        // Redirect to appropriate page based on offered_mode
        if ($offered_mode === 'online') {
            header("Location: ../../learner/pages/CourseContent.php?course_id=$course_id");
        } else {
            header("Location: Course.php?program_id=$program_id");
        }
        exit;
    } elseif (isset($_POST['unenroll'])) {
        // Unenroll the learner from the course
        $conn->query("DELETE FROM enrollments WHERE learner_id = '$learner_id' AND course_id = '$course_id'");
        header("Location: Course.php?program_id=$program_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($program['program_name']); ?> - Learner Interface</title>
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../learner/assets/css/Course.css">
    <script>
        // Confirmation dialog for unenroll button
        function confirmUnenroll(event) {
            if (!confirm("Are you sure you want to unenroll from this course?")) {
                event.preventDefault(); // Prevent form submission if not confirmed
            }
        }

        // Toggle description visibility
        function toggleDescription(courseId) {
            const moreText = document.getElementById('more-' + courseId);
            const toggleLink = document.getElementById('toggle-' + courseId);

            if (moreText.style.display === "none") {
                moreText.style.display = "inline";
                toggleLink.textContent = "See Less";
            } else {
                moreText.style.display = "none";
                toggleLink.textContent = "See More";
            }
        }
    </script>
</head>

<body class="bg-light-gray">
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <!-- Title Section -->
    <div class="bg-white text-center py-4 shadow-sm">
        <h1 class="fw-bold text-primary"><?php echo htmlspecialchars($program['program_name']); ?></h1>
    </div>

    <!-- Courses Container -->
    <div class="container my-5">
        <div class="row g-4">
            <?php
            // Fetch approved courses associated with the program_id, excluding archived courses
            $query = $conn->query("
                SELECT course_id, course_name, course_img, course_desc, course_date, start_date, end_date, offered_mode, enable_registration, course_time 
                FROM courses 
                WHERE program_id = '$program_id' AND status = 'approved' AND archive = FALSE
            ");

            // Loop through each course and display it
            if ($query->num_rows > 0) {
                while ($course = $query->fetch_assoc()) {
                    // Check if the learner is enrolled in the course
                    $course_id = $course['course_id'];
                    $offered_mode = $course['offered_mode']; // Fetch the mode of the course (face-to-face or online)
                    $enrollment_query = $conn->query("SELECT * FROM enrollments WHERE learner_id = '$learner_id' AND course_id = '$course_id'");
                    $is_enrolled = $enrollment_query->num_rows > 0;

                    // Determine if "See More/See Less" should be displayed
                    $course_desc = htmlspecialchars($course['course_desc']);
                    $show_toggle = strlen($course_desc) > 100;

                    // Determine the course date or duration to display
                    if ($offered_mode === 'face_to_face') {
                        $course_date = htmlspecialchars(date('F d, Y', strtotime($course['course_date'])));
                        $date_label = "Date";
                    } else {
                        $course_date = htmlspecialchars(date('F d, Y', strtotime($course['start_date'])) . ' - ' . date('F d, Y', strtotime($course['end_date'])));
                        $date_label = "Duration";
                    }

                    $course_time = isset($course['course_time']) ? htmlspecialchars($course['course_time']) : 'Not Specified';

                    // Calculate learner's progress for online courses
                    if ($is_enrolled && $offered_mode === 'online') {
                        // Assuming we have a table for tracking completion of sections (pre-test, learning materials, quiz, post-test)
                        $progress_query = $conn->query("
                            SELECT 
                                (SELECT COUNT(*) FROM pre_test_results WHERE learner_id = '$learner_id' AND course_id = '$course_id') AS pre_test,
                                (SELECT COUNT(*) FROM quiz_results WHERE learner_id = '$learner_id' AND course_id = '$course_id') AS quiz,
                                (SELECT COUNT(*) FROM post_test_results WHERE learner_id = '$learner_id' AND course_id = '$course_id') AS post_test
                        ");
                        $progress_data = $progress_query->fetch_assoc();

                        // Fetch the total number of learning materials for the course
                        $learning_materials_query = $conn->query("SELECT LM_id FROM learning_materials WHERE course_id = '$course_id'");
                        $total_learning_materials = $learning_materials_query->num_rows;


                        // Fetch the count of completed learning materials for the learner
                        $completed_materials_query = $conn->query("SELECT COUNT(*) AS completed FROM module_completion WHERE learner_id = '$learner_id' AND course_id = '$course_id'");
                        $completed_materials_data = $completed_materials_query->fetch_assoc();
                        $completed_materials = $completed_materials_data['completed'];


                        // Calculate the total number of sections (Pre-test, Materials, Quiz, Post-test)
                        $total_sections = 4; // Pre-test, Materials, Quiz, Post-test
            
                        // Count completed sections (pre-test, materials, quiz, post-test)
                        $completed_sections = 0;
                        if ($progress_data['pre_test'] > 0) {
                            $completed_sections++;
                        }
                        if ($completed_materials == $total_learning_materials) {
                            $completed_sections++; // Completed learning materials count
                        }
                        if ($progress_data['quiz'] > 0) {
                            $completed_sections++;
                        }
                        if ($progress_data['post_test'] > 0) {
                            $completed_sections++;
                        }

                        // Calculate the percentage of completion
                        $progress_percentage = ($completed_sections / $total_sections) * 100;
                    } else {
                        $progress_percentage = 0;

                    }
                    ?>
                    <!-- Course display HTML here -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm <?php echo $is_enrolled ? 'border-success' : ''; ?>">
                            <?php if ($is_enrolled && $offered_mode === 'online'): ?>
                                <a href="../../learner/pages/CourseContent.php?course_id=<?php echo $course_id; ?>"
                                    class="text-decoration-none">
                                <?php endif; ?>
                                <img src="../../staff/upload/<?php echo htmlspecialchars($course['course_img']); ?>"
                                    class="card-img-top rounded" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                                <?php if ($is_enrolled && $offered_mode === 'online'): ?>
                                </a>
                            <?php endif; ?>
                            <div class="card-body">
                                <?php if ($is_enrolled && $offered_mode === 'online'): ?>
                                    <a href="../../learner/pages/CourseContent.php?course_id=<?php echo $course_id; ?>"
                                        class="text-decoration-none">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                    </a>
                                <?php else: ?>
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                <?php endif; ?>
                                <p class="text-muted small mb-2">
                                    <strong><?php echo $date_label; ?>:</strong> <?php echo $course_date; ?>
                                </p>
                                <p class="text-muted small mb-2">
                                    <strong>Mode:</strong>
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $offered_mode))); ?>
                                </p>
                                <?php if ($offered_mode === 'face_to_face'): ?>
                                    <p class="text-muted small mb-2">
                                        <strong>Time:</strong> <?php echo htmlspecialchars($course_time); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="card-text text-muted">
                                    <?php if ($show_toggle): ?>
                                        <span class="course-desc">
                                            <?php echo mb_substr($course_desc, 0, 100); ?>
                                        </span>
                                        <span class="more-text" id="more-<?php echo $course_id; ?>" style="display: none;">
                                            <?php echo mb_substr($course_desc, 100); ?>
                                        </span>
                                        <a href="javascript:void(0);" id="toggle-<?php echo $course_id; ?>"
                                            onclick="toggleDescription(<?php echo $course_id; ?>)">
                                            See More
                                        </a>
                                    <?php else: ?>
                                        <?php echo $course_desc; ?>
                                    <?php endif; ?>
                                </p>
                                <!-- Progress Bar for Online Course -->
                                <?php if ($offered_mode === 'online'): ?>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $progress_percentage; ?>%"
                                            aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0"
                                            aria-valuemax="100">
                                            <?php echo round($progress_percentage); ?>% Completed
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Enrollment Button -->
                                <?php if ($is_enrolled): ?>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                        <input type="hidden" name="offered_mode" value="<?php echo $offered_mode; ?>">
                                        <button type="submit" name="unenroll" class="btn btn-danger"
                                            onclick="confirmUnenroll(event)">Unenroll</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                        <input type="hidden" name="offered_mode" value="<?php echo $offered_mode; ?>">
                                        <button type="submit" name="enroll" class="btn btn-primary">Enroll</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($is_enrolled && $offered_mode === 'face_to_face'): ?>
                                    <div class="face-to-face-options mt-3">
                                        <?php if ($course['enable_registration']): ?>
                                            <a href="register.php?course_id=<?php echo $course_id; ?>"
                                                class="btn btn-info btn-sm">Registration</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No courses available for this program.</p>";
            }
            ?>
        </div>
    </div>

</body>

</html>