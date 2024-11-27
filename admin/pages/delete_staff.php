<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Get the staff_id from the query string
$staff_id = $_GET['staff_id'] ?? null;

if ($staff_id) {
    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM staff_accounts WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Staff account deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete staff account.";
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid staff ID.";
}

// Redirect back to the manage_staff.php page
header('Location: manage_staff.php');
exit;
