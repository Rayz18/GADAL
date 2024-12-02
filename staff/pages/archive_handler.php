<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Check if program_id or course_id is provided
if (isset($_GET['program_id'])) {
    $program_id = intval($_GET['program_id']);
    $update_query = $conn->prepare("UPDATE programs SET archive = TRUE WHERE program_id = ?");
    $update_query->bind_param('i', $program_id);
    $update_query->execute();
    header('Location: manage_programs.php?message=Program archived successfully');
    exit;
} elseif (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $update_query = $conn->prepare("UPDATE courses SET archive = TRUE WHERE course_id = ?");
    $update_query->bind_param('i', $course_id);
    $update_query->execute();
    header('Location: manage_programs.php?message=Course archived successfully');
    exit;
} else {
    header('Location: manage_programs.php?error=Invalid request');
    exit;
}
?>