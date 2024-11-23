<?php
// Start the session
session_start();

// Redirect based on user role if already logged in
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];

    if ($role === 'admin') {
        header("Location: admin/pages/admin_dashboard.php");
        exit();
    } elseif ($role === 'staff') {
        header("Location: staff/pages/staff_dashboard.php");
        exit();
    } elseif ($role === 'learner') {
        header("Location: Home.php");
        exit();
    }
}

// If no role is detected, default to the Home.php page for all users
header("Location: Home.php");
exit();