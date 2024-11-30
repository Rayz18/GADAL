<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$learner_id = $_SESSION['learner_id'];
$course_id = $_POST['course_id'];

// Fetch all post-test questions for validation
$query = "SELECT * FROM post_test_questions WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$total_questions = $result->num_rows;
$correct_answers = 0;

while ($row = $result->fetch_assoc()) {
    $question_id = $row['post_test_id'];
    if (isset($_POST['answers'][$question_id]) && $_POST['answers'][$question_id] === $row['correct_option']) {
        $correct_answers++;
    }
}

// Calculate score
$score = ($correct_answers / $total_questions) * 100;

// Insert post-test results
$query = "INSERT INTO post_test_results (learner_id, course_id, score, total_questions, correct_answers, date_taken)
          VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiddi", $learner_id, $course_id, $score, $total_questions, $correct_answers);
$stmt->execute();

header("Location: CourseContent.php?course_id=$course_id&tab=post-test");
exit();
