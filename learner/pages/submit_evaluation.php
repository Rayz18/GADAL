<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $learner_id = isset($_SESSION['learner_id']) ? intval($_SESSION['learner_id']) : 0;

    // Collect answers to questions (default to 0 if not set)
    $answers = [];
    for ($i = 1; $i <= 12; $i++) {
        $answers["question_$i"] = isset($_POST["question_$i"]) ? intval($_POST["question_$i"]) : 0;
    }

    // Other inputs
    $helpful_feedback = isset($_POST['helpful_feedback']) ? $_POST['helpful_feedback'] : '';
    $helpful_aspect = isset($_POST['helpful_aspect']) ? $_POST['helpful_aspect'] : '';
    $comments = isset($_POST['comments']) ? $_POST['comments'] : '';

    // Prepare the SQL query
    $query = $conn->prepare("
        INSERT INTO evaluations 
        (course_id, learner_id, question_1, question_2, question_3, question_4, question_5, 
         question_6, question_7, question_8, question_9, question_10, question_11, question_12, 
         helpful_feedback, helpful_aspect, comments) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$query) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $bind_success = $query->bind_param(
        "iiiiiiiiiiiiiisss",
        $course_id,
        $learner_id,
        $answers['question_1'],
        $answers['question_2'],
        $answers['question_3'],
        $answers['question_4'],
        $answers['question_5'],
        $answers['question_6'],
        $answers['question_7'],
        $answers['question_8'],
        $answers['question_9'],
        $answers['question_10'],
        $answers['question_11'],
        $answers['question_12'],
        $helpful_feedback,
        $helpful_aspect,
        $comments
    );

    if (!$bind_success) {
        die("Bind failed: " . $query->error);
    }

    // Execute the query
    if ($query->execute()) {
        header("Location: success_page.php?message=Evaluation submitted!");
        exit();
    } else {
        die("Execution failed: " . $query->error);
    }
}
