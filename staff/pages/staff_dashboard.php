<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch all programs
$programs_query = $conn->query("SELECT * FROM programs");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../staff/assets/css/staff_dashboard.css">
</head>

<body>
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <!-- Main Content Section -->
    <main class="flex-grow-1 d-flex flex-column align-items-center justify-content-center bg-gray p-5">
        <h1 class="dashboard-heading">STAFF DASHBOARD - SUMMARY VIEW</h1>

        <!-- Programs Summary Section -->
        <div class="programs-summary mt-4 w-100">
            <h2 class="text-light-purple">PROGRAMS OVERVIEW</h2>

            <div class="row mt-3">
                <?php while ($program = $programs_query->fetch_array()) { ?>
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h3 class="card-title text-primary">
                                    <?php echo $program['program_name']; ?>
                                </h3>
                                <h4 class="mt-3">Courses:</h4>
                                <ul class="list-unstyled">
                                    <?php
                                    $courses_query = $conn->query("SELECT * FROM courses WHERE program_id = " . $program['program_id']);
                                    if ($courses_query->num_rows > 0) {
                                        while ($course = $courses_query->fetch_array()) { ?>
                                            <li class="course-item bg-light rounded p-2 my-2 d-flex justify-content-between">
                                                <?php echo $course['course_name']; ?>
                                                <span class="text-muted">(<?php echo $course['course_date']; ?>)</span>
                                            </li>
                                        <?php }
                                    } else { ?>
                                        <li class="text-danger">No courses available for this program.</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleButton = document.getElementById("sidebar-toggle");
            const sidebar = document.querySelector(".sidebar");
            const mainContent = document.querySelector("main");

            // Initialize sidebar state
            sidebar.classList.add("collapsed");
            toggleButton.style.left = "0"; // Align toggle with collapsed sidebar
            mainContent.style.marginLeft = "0";

            toggleButton.addEventListener("click", function () {
                sidebar.classList.toggle("collapsed");

                if (sidebar.classList.contains("collapsed")) {
                    mainContent.style.marginLeft = "0";
                    toggleButton.style.left = "0"; // Move toggle back to collapsed position
                } else {
                    mainContent.style.marginLeft = "250px";
                    toggleButton.style.left = "250px"; // Move toggle alongside expanded sidebar
                }
            });
        });

    </script>

</body>

</html>