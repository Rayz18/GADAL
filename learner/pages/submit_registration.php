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

    $query = $conn->prepare("
        INSERT INTO registrations 
        (course_id, learner_id, name, age, gender, position_designation, office_affiliation, contact_number, email_address) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
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
        header("Location: success_page.php?message=Registration successful!");
        exit();
    } else {
        echo "Error: " . $query->error;
    }
}
?>