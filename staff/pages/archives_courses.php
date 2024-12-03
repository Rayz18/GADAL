<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course_id'])) {
    $course_id = $_POST['delete_course_id'];

    // Prepare DELETE query to remove the course
    $delete_query = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $delete_query->bind_param("i", $course_id);

    // Execute the delete query
    if ($delete_query->execute()) {
        $delete_success = "Course deleted successfully.";
    } else {
        $delete_error = "Error deleting course: " . $conn->error;
    }
}

// Handle restore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_course_id'])) {
    $course_id = $_POST['restore_course_id'];

    // Prepare UPDATE query to restore the course
    $restore_query = $conn->prepare("UPDATE courses SET archive = FALSE WHERE course_id = ?");
    $restore_query->bind_param("i", $course_id);

    // Execute the restore query
    if ($restore_query->execute()) {
        $restore_success = "Course restored successfully.";
    } else {
        $restore_error = "Error restoring course: " . $conn->error;
    }
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

        .page-title-container {
            width: 100%;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border-bottom: 2px solid #d3d3d3;
            padding-top: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            color: blue;
            font-weight: bold;
            text-align: center;
            font-size: 2.5rem;
            margin: 0;
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

<body>

<div class="layout">
    <!-- Sidebar -->
    <div id="toggle-sidebar" class="toggle-sidebar">
        <!-- Sidebar content can go here -->
    </div>
    <?php include '../../public/includes/StaffNavBar.php'; ?>
    <?php include '../../public/includes/header.php'; ?>
    <!-- Main Content -->
    <div id="content" class="content">
        <!-- Toggle Sidebar Icon -->
        <div id="toggle-sidebar" class="toggle-sidebar"></div>

        <!-- Page Title Section -->
        <div class="page-title-container">
            <h1 class="page-title">ARCHIVED COURSES</h1>
        </div>

        <!-- Toast Notification -->
        <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
            <?php if (isset($delete_success)): ?>
                <div id="success-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?php echo htmlspecialchars($delete_success); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php elseif (isset($delete_error)): ?>
                <div id="error-toast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?php echo htmlspecialchars($delete_error); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php elseif (isset($restore_success)): ?>
                <div id="restore-success-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?php echo htmlspecialchars($restore_success); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php elseif (isset($restore_error)): ?>
                <div id="restore-error-toast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?php echo htmlspecialchars($restore_error); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
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
                                    <img src="<?php echo $course_img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-img">No Image Available</div>
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
                                <div class="card-body">
                                        <form method="GET" action="restore_handler.php" class="d-inline">
                                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">Restore</button>
                                        </form>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirmDelete();">
                                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php }
                } else {
                    echo "<p>No archived courses available.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>

    function confirmDelete() {
            return confirm("Are you sure you want to delete this program? This action cannot be undone.");
        }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Toast
        var toastSuccess = new bootstrap.Toast(document.getElementById('success-toast'));
        var toastError = new bootstrap.Toast(document.getElementById('error-toast'));
        var toastRestoreSuccess = new bootstrap.Toast(document.getElementById('restore-success-toast'));
        var toastRestoreError = new bootstrap.Toast(document.getElementById('restore-error-toast'));

        // Show success/error toast notifications
        if (document.getElementById('success-toast')) toastSuccess.show();
        if (document.getElementById('error-toast')) toastError.show();
        if (document.getElementById('restore-success-toast')) toastRestoreSuccess.show();
        if (document.getElementById('restore-error-toast')) toastRestoreError.show();
    });
</script>

</body>
</html>