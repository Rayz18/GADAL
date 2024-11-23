<?php
session_start(); // Start session before any output
require_once "../../config/config.php";

// Check if learner is logged in
if (!isset($_SESSION['learner_id'])) {
    // Redirect to login page if not logged in
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
    if (isset($_POST['enroll'])) {
        $conn->query("INSERT INTO enrollments (learner_id, course_id) VALUES ('$learner_id', '$course_id')");
    } elseif (isset($_POST['unenroll'])) {
        $conn->query("DELETE FROM enrollments WHERE learner_id = '$learner_id' AND course_id = '$course_id'");
    }
    // Redirect to avoid form resubmission
    header("Location: Course.php?program_id=$program_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($program['program_name']); ?> - Learner Interface</title>
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="../../learner/assets/css/Course.css">
    <script>
        // Confirmation dialog for unenroll button
        function confirmUnenroll(event) {
            if (!confirm("Are you sure you want to unenroll from this course?")) {
                event.preventDefault(); // Prevent form submission if not confirmed
            }
        }
    </script>
</head>

<body class="bg-light-gray">
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <!-- Title Section -->
    <div class="title-section">
        <h1><?php echo htmlspecialchars($program['program_name']); ?></h1>
    </div>

    <!-- Courses Container -->
    <div class="courses-container">
        <?php
        // Fetch approved courses associated with the program_id
        $query = $conn->query("SELECT * FROM courses WHERE program_id = '$program_id' AND status = 'approved'");

        // Loop through each course and display it
        if ($query->num_rows > 0) {
            while ($course = $query->fetch_assoc()) {
                // Check if the learner is enrolled in the course
                $course_id = $course['course_id'];
                $enrollment_query = $conn->query("SELECT * FROM enrollments WHERE learner_id = '$learner_id' AND course_id = '$course_id'");
                $is_enrolled = $enrollment_query->num_rows > 0;
                ?>
                <div class="course-card">
                    <div class="course-image">
                        <img src="../../staff/upload/<?php echo htmlspecialchars($course['course_img']); ?>"
                            alt="<?php echo htmlspecialchars($course['course_name']); ?> Image">
                    </div>
                    <div class="course-description">
                        <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
                        <p><?php echo htmlspecialchars($course['course_desc']); ?></p>
                        <p class="course-date"><?php echo htmlspecialchars($course['course_date']); ?></p>

                        <!-- Form moved here -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <?php if ($is_enrolled): ?>
                                <button type="submit" name="unenroll" class="button unenroll"
                                    onclick="confirmUnenroll(event)">Unenroll</button>
                                <a href="../../learner/pages/CourseContent.php?course_id=<?php echo $course_id; ?>"
                                    class="button view-course">View Course</a>
                            <?php else: ?>
                                <button type="submit" name="enroll" class="button enroll">Enroll</button>
                            <?php endif; ?>
                        </form>
                        <!-- End of moved form -->
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No approved courses available for this program.</p>";
        }
        ?>
    </div>
</body>

</html>