<?php
// Check if a session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php'); // Redirect to login page if not logged in
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
    <link rel="stylesheet" href="../../public/assets/css/AdminNavBar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <!-- Logo Image at the center top -->
            <a href="admin_dashboard.php" class="logo">
                <img src="../../public/assets/images/GAD.png" alt="GAD Logo" class="user-logo">
            </a>

            <!-- Title below the logo -->
            <div class="sidebar-title">
                <p>Batangas State University - TNEU</p>
                <p>Gender and Development</p>
            </div>
        </div>
        <!-- Navigation Links -->
        <ul class="menu">
            <li><a href="admin_dashboard.php" class="menu-item">Dashboard</a></li>
            <li><a href="manage_staff.php" class="menu-item">Manage Staff</a></li>
            <li><a href="content_moderation.php" class="menu-item">Content Moderation</a></li>
            <li><a href="#" class="menu-item">Manage</a></li>
            <li><a href="#" class="menu-item">Reports</a></li>
        </ul>

        <!-- Logout Button -->
        <a href="admin_logout.php" class="logout" onclick="confirmLogout()">Logout</a>
    </div>

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-toggle" id="sidebar-toggle"></div>

    <!-- JavaScript to handle the sidebar toggle -->
    <script>
        document.getElementById("sidebar-toggle").addEventListener("click", function () {
            document.getElementById("sidebar").classList.toggle("expanded");
            document.getElementById("content").classList.toggle("shifted");
        });

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "admin_logout.php";
            }
        }
    </script>
</body>

</html>