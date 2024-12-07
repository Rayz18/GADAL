<?php
session_start(); // Ensure the session is started
require_once '../../config/config.php'; // Include database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['module_id'], $_POST['course_id'])) {
    if (!isset($_SESSION['learner_id'])) {
        // Redirect if learner is not logged in
        header("Location: login.php");
        exit;
    }

    $learner_id = $_SESSION['learner_id']; // Get learner ID from session
    $module_id = $_POST['module_id']; // Get module ID from form submission
    $course_id = $_POST['course_id']; // Get course ID from form submission

    // Insert or update module completion record
    $query = "INSERT INTO module_completion (learner_id, LM_id, course_id) VALUES (?, ?, ?)
              ON DUPLICATE KEY UPDATE completed_at = CURRENT_TIMESTAMP";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $learner_id, $module_id, $course_id);

    if ($stmt->execute()) {
        // Redirect back to the Learning Materials page after marking done
        header("Location: CourseContent.php?course_id=$course_id&tab=learning-materials&status=success");
        exit;
    } else {
        // Redirect with error if the query fails
        header("Location: CourseContent.php?course_id=$course_id&tab=learning-materials&status=error");
        exit;
    }
} else {
    // Redirect if accessed without POST or required data is missing
    header("Location: CourseContent.php?tab=learning-materials&status=invalid_request");
    exit;
}
