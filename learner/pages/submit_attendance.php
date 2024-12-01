<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $name = $_POST['name'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $position_designation = $_POST['position_designation'];
    $office_affiliation = $_POST['office_affiliation'];
    $contact_number = $_POST['contact_number'];
    $email_address = $_POST['email_address'];

    $learner_id = $_SESSION['learner_id']; // Ensure learner_id is stored in session after login

    // Insert attendance details into the database
    $query = $conn->prepare("
        INSERT INTO attendance 
        (course_id, learner_id, name, age, gender, position_designation, office_affiliation, contact_number, email_address, is_completed) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    $query->bind_param(
        "iisssssss",
        $course_id,
        $learner_id,
        $name,
        $age,
        $gender,
        $position_designation,
        $office_affiliation,
        $contact_number,
        $email_address
    );

    if ($query->execute()) {
        // Redirect to evaluation form
        header("Location: ../pages/success_page.php?message=Attendance completed!");
        exit();
    } else {
        echo "Error: " . $query->error;
    }
}
?>