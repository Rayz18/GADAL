<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Base query to fetch programs
$query = "SELECT * FROM programs WHERE 1";

// Handle filter
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    $today = date('Y-m-d');
    $last7days = date('Y-m-d', strtotime('-7 days'));
    $last30days = date('Y-m-d', strtotime('-30 days'));
    $lastYear = date('Y-m-d', strtotime('-1 year'));

    if ($filter === 'today') {
        $query .= " AND DATE(created_at) = '$today'";
    } elseif ($filter === 'last7days') {
        $query .= " AND DATE(created_at) >= '$last7days'";
    } elseif ($filter === 'last30days') {
        $query .= " AND DATE(created_at) >= '$last30days'";
    } elseif ($filter === 'lastYear') {
        $query .= " AND DATE(created_at) >= '$lastYear'";
    }
}

// Handle search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query .= " AND program_name LIKE '%$search%'";
}

// Add order by clause
$query .= " ORDER BY created_at DESC";

// Execute the query
$programs_query = $conn->query($query);

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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Programs Overview Heading -->
                <h2 class="text-light-purple mb-0">PROGRAMS OVERVIEW</h2>

                <!-- Filter and Search Form -->
                <form method="GET" class="d-flex align-items-center">
                    <!-- Filter Dropdown -->
                    <div class="form-group mb-0 mr-3">
                        <label for="filter" class="sr-only">Filter by:</label>
                        <select name="filter" id="filter" class="custom-form-control">
                            <option value="">All</option>
                            <option value="today" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'today') ? 'selected' : ''; ?>>Today</option>
                            <option value="last7days" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'last7days') ? 'selected' : ''; ?>>Last 7 Days</option>
                            <option value="last30days" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'last30days') ? 'selected' : ''; ?>>Last 30 Days</option>
                            <option value="lastYear" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'lastYear') ? 'selected' : ''; ?>>Last Year</option>
                        </select>
                    </div>

                    <!-- Search Bar -->
                    <div class="form-group mb-0 mr-3">
                        <label for="search" class="sr-only">Search Programs:</label>
                        <input type="text" name="search" id="search" class="custom-form-control"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                            placeholder="Enter program name">
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mb-0">
                        <button type="submit" class="custom-button">Apply</button>
                    </div>
                </form>
            </div>

            <div class="row mt-3">
                <?php if ($programs_query->num_rows > 0): ?>
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
                <?php else: ?>
                    <p class="text-center text-danger">No programs found. Please adjust your filter or search criteria.</p>
                <?php endif; ?>
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