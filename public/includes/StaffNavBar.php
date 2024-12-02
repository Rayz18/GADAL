<?php
// Check if a session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/StaffNavBar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../../public/assets/js/staffToggle.js" defer></script>

</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <!-- Logo Image at the center top -->
            <a href="staff_dashboard.php" class="logo">
                <img src="../../public/assets/images/GAD.png" alt="Staff Logo" class="logo-img">
            </a>

            <!-- Title below the logo -->
            <div class="sidebar-title">
                <p>Batangas State University - TNEU</p>
                <p>Gender and Development</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <ul class="menu">
            <li><a href="staff_dashboard.php" class="menu-item">Home</a></li>
            <li><a href="manage_programs.php" class="menu-item">Manage Programs</a></li>
            <li><a href="archives_programs.php" class="menu-item">Archived Programs</a></li>
            <li><a href="archives_courses.php" class="menu-item">Archived Courses</a></li>
        </ul>

        <!-- Logout Button -->
        <a href="staff_logout.php" class="logout">Logout</a>
    </div>

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-toggle" id="sidebar-toggle"></div>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "staff_logout.php";
            }
        }
    </script>

</body>

</html>