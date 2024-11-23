<?php
include '../../config/config.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$seminar_id = $_GET['seminar_id'];
$course_id = $_GET['course_id'];  // Retrieve course_id for redirection

$query = "DELETE FROM seminars WHERE seminar_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seminar_id);

if ($stmt->execute()) {
    header("Location: add_seminar.php?course_id=$course_id&success=Seminar+deleted+successfully");
} else {
    header("Location: add_seminar.php?course_id=$course_id&error=Failed+to+delete+seminar");
}
