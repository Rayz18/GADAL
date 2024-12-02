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
                SELECT course_id, course_name, course_img, course_desc, course_date, start_date, end_date, offered_mode, enable_registration 
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
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm <?php echo $is_enrolled ? 'border-success' : ''; ?>">
                            <?php if ($is_enrolled && $offered_mode === 'online'): ?>
                                <!-- Make the course image clickable if enrolled and online -->
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
                                    <!-- Make the course title clickable if enrolled and online -->
                                    <a href="../../learner/pages/CourseContent.php?course_id=<?php echo $course_id; ?>"
                                        class="text-decoration-none">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                    </a>
                                <?php else: ?>
                                    <!-- Display the title as plain text if not enrolled or not online -->
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                <?php endif; ?>
                                <p class="text-muted small mb-2">
                                    <strong><?php echo $date_label; ?>:</strong> <?php echo $course_date; ?>
                                </p>
                                <p class="text-muted small mb-2">
                                    <strong>Mode:</strong>
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $offered_mode))); ?>
                                </p>
                                <p class="card-text text-muted">
                                    <?php if ($show_toggle): ?>
                                        <span class="course-desc">
                                            <?php echo mb_substr($course_desc, 0, 100); ?>
                                        </span>
                                        <span class="more-text" id="more-<?php echo $course_id; ?>" style="display: none;">
                                            <?php echo mb_substr($course_desc, 100); ?>
                                        </span>
                                        <a href="javascript:void(0);" class="see-more-link" id="toggle-<?php echo $course_id; ?>"
                                            onclick="toggleDescription('<?php echo $course_id; ?>')">
                                            See More
                                        </a>
                                    <?php else: ?>
                                        <?php echo $course_desc; ?>
                                    <?php endif; ?>
                                </p>
                                <form method="POST">
                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                    <input type="hidden" name="offered_mode" value="<?php echo $offered_mode; ?>">
                                    <?php if ($is_enrolled): ?>
                                        <button type="submit" name="unenroll" class="btn btn-danger btn-sm"
                                            onclick="confirmUnenroll(event)">Unenroll</button>
                                    <?php else: ?>
                                        <button type="submit" name="enroll" class="btn btn-primary btn-sm">Enroll</button>
                                    <?php endif; ?>
                                </form>
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
                echo "<p class='text-center text-muted'>No approved courses available for this program.</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>