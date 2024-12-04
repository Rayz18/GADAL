<?php
session_start();

// Ensure the module index is provided
if (isset($_POST['module_index'])) {
    $module_index = intval($_POST['module_index']);

    // If the session array does not exist, initialize it
    if (!isset($_SESSION['completed_modules'])) {
        $_SESSION['completed_modules'] = [];
    }

    // Mark the module as done
    $_SESSION['completed_modules'][$module_index] = true;

    // Redirect back to the learning materials tab
    header("Location: CourseContent.php?course_id=" . $_GET['course_id'] . "&tab=learning-materials");
    exit();
} else {
    // If no module index is provided, redirect back with an error message
    echo "<script>alert('Invalid request'); window.location.href='CourseContent.php?course_id=" . $_GET['course_id'] . "&tab=learning-materials';</script>";
}
?>