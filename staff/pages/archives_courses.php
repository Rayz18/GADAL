<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Fetch archived courses
$archived_courses_query = $conn->query("
    SELECT course_id, course_name, course_img, course_desc, course_date, start_date, end_date, offered_mode, program_name 
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
    <title>Archived Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .placeholder-img {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
            font-size: 0.875rem;
            color: #6c757d;
            background-color: #e9ecef;
            border-radius: 5px;
        }
    </style>
    <script>
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
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <!-- Title Section -->
    <div class="bg-white text-center py-4 shadow-sm">
        <h1 class="fw-bold text-primary">Archived Courses</h1>
    </div>

    <!-- Archived Courses Container -->
    <div class="container my-5">
        <div class="row g-4">
            <?php
            if ($archived_courses_query->num_rows > 0) {
                while ($course = $archived_courses_query->fetch_assoc()) {
                    // Determine the course date or duration to display
                    $offered_mode = $course['offered_mode'];
                    if ($offered_mode === 'face_to_face') {
                        $course_date = htmlspecialchars(date('F d, Y', strtotime($course['course_date'])));
                        $date_label = "Date";
                    } else {
                        $course_date = htmlspecialchars(date('F d, Y', strtotime($course['start_date'])) . ' - ' . date('F d, Y', strtotime($course['end_date'])));
                        $date_label = "Duration";
                    }

                    // Determine if "See More/See Less" should be displayed
                    $course_desc = htmlspecialchars($course['course_desc']);
                    $show_toggle = strlen($course_desc) > 100;

                    // Placeholder image if course_img is missing
                    $course_img = $course['course_img'] ? "../../staff/upload/" . htmlspecialchars($course['course_img']) : null;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($course_img): ?>
                                <img src="<?php echo $course_img; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-img">
                                    No Image Available
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                <p class="text-muted small mb-2"><strong>Program:</strong>
                                    <?php echo htmlspecialchars($course['program_name']); ?></p>
                                <p class="text-muted small mb-2"><strong><?php echo $date_label; ?>:</strong>
                                    <?php echo $course_date; ?></p>
                                <p class="text-muted small mb-2"><strong>Mode:</strong>
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $offered_mode))); ?></p>
                                <p class="card-text text-muted">
                                    <?php if ($show_toggle): ?>
                                        <span class="course-desc">
                                            <?php echo mb_substr($course_desc, 0, 100); ?>
                                        </span>
                                        <span class="more-text" id="more-<?php echo $course['course_id']; ?>"
                                            style="display: none;">
                                            <?php echo mb_substr($course_desc, 100); ?>
                                        </span>
                                        <a href="javascript:void(0);" class="see-more-link"
                                            id="toggle-<?php echo $course['course_id']; ?>"
                                            onclick="toggleDescription('<?php echo $course['course_id']; ?>')">
                                            See More
                                        </a>
                                    <?php else: ?>
                                        <?php echo $course_desc; ?>
                                    <?php endif; ?>
                                </p>
                                <form method="GET" action="restore_handler.php">
                                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Restore</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center text-muted'>No archived courses available.</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>