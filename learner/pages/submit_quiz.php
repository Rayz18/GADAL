<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $learner_id = $_SESSION['learner_id'];
    $course_id = $_POST['course_id'];
    $answers = $_POST['answers'];

    $query = "SELECT * FROM quiz_questions WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $questions = $stmt->get_result();

    $total_questions = $questions->num_rows;
    $correct_answers = 0;

    while ($question = $questions->fetch_assoc()) {
        $quiz_id = $question['quiz_id'];
        if (isset($answers[$quiz_id]) && $answers[$quiz_id] === $question['correct_option']) {
            $correct_answers++;
        }
    }

    $score = ($correct_answers / $total_questions) * 100;

    $query = "INSERT INTO quiz_results (learner_id, course_id, score, total_questions, correct_answers, date_taken) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiddi", $learner_id, $course_id, $score, $total_questions, $correct_answers);
    $stmt->execute();

    header("Location: CourseContent.php?course_id=$course_id&tab=quiz");
    exit;
}
?>