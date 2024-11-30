<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

if (!isset($_POST['course_id']) || !isset($_POST['answers'])) {
    die("Course ID or answers are missing.");
}

$course_id = $_POST['course_id'];
$learner_id = $_SESSION['learner_id'];
$submitted_answers = $_POST['answers'];

// Fetch the correct answers from the database
$query = "SELECT pre_test_id, correct_option FROM pre_test_questions WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables for scoring
$total_questions = $result->num_rows;
$correct_answers = 0;

// Check the submitted answers
while ($row = $result->fetch_assoc()) {
    $pre_test_id = $row['pre_test_id'];
    $correct_option = $row['correct_option'];
    if (isset($submitted_answers[$pre_test_id]) && $submitted_answers[$pre_test_id] === $correct_option) {
        $correct_answers++;
    }
}

// Calculate the score as a percentage with 2 decimal points
$score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0.00;

// Insert the result into the database
$query = "INSERT INTO pre_test_results (learner_id, course_id, score, total_questions, correct_answers, date_taken) 
          VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $learner_id, $course_id, $score, $total_questions, $correct_answers);

if ($stmt->execute()) {
    // Redirect to the results page
    header("Location: pre_test_results.php?course_id=" . urlencode($course_id) . "&score=" . urlencode($score) . "&correct=" . urlencode($correct_answers) . "&total=" . urlencode($total_questions));
    exit;
} else {
    die("Failed to save pre-test results.");
}
?>