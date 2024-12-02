<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Handle restore requests
if (isset($_GET['program_id'])) {
    $program_id = intval($_GET['program_id']);
    $restore_query = $conn->prepare("UPDATE programs SET archive = FALSE WHERE program_id = ?");
    $restore_query->bind_param('i', $program_id);
    $restore_query->execute();
    header('Location: archives.php?message=Program restored successfully');
    exit;
} elseif (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $restore_query = $conn->prepare("UPDATE courses SET archive = FALSE WHERE course_id = ?");
    $restore_query->bind_param('i', $course_id);
    $restore_query->execute();
    header('Location: archives.php?message=Course restored successfully');
    exit;
} else {
    header('Location: archives.php?error=Invalid request');
    exit;
}
?>