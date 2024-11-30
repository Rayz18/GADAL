<?php
session_start();

if (!isset($_GET['score']) || !isset($_GET['course_id']) || !isset($_GET['correct']) || !isset($_GET['total'])) {
    die("Required information is missing.");
}

$score = htmlspecialchars($_GET['score']);
$course_id = htmlspecialchars($_GET['course_id']);
$correct_answers = htmlspecialchars($_GET['correct']);
$total_questions = htmlspecialchars($_GET['total']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Test Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center">
            <h1 class="mb-4">Pre-Test Results</h1>
            <p class="lead">Your performance:</p>
            <h2 class="display-4 text-success"><?php echo $score; ?>%</h2>
            <p class="lead">Correct Answers: <strong><?php echo $correct_answers; ?></strong> /
                <?php echo $total_questions; ?></p>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=learning-materials"
                class="btn btn-primary mt-4">
                Proceed to Learning Materials
            </a>
        </div>
    </div>
</body>

</html>