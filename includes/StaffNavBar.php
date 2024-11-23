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

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <!-- Logo Image at the center top -->
        <a href="staff_dashboard.php" class="logo">
            <img src="../../assets/images/GAD.png" alt="Staff Logo" class="logo-img">
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
    </ul>

    <!-- Logout Button -->
    <a href="staff_logout.php" class="logout">Logout</a>
</div>

<!-- Sidebar Toggle Button -->
<div class="sidebar-toggle" id="sidebar-toggle"></div>