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
    <title>Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            margin-top: 50px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .lead {
            font-size: 1.25rem;
        }

        .display-4 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .btn-primary {
            background-color: #9E8BB8;
            border: none;
        }

        .btn-primary:hover {
            background-color: #7C6A98;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center">
            <h1 class="mb-4">Quiz Results</h1>
            <p class="lead">Your performance:</p>
            <h2 class="display-4 text-success"><?php echo $score; ?>%</h2>
            <p class="lead">Correct Answers:
                <strong><?php echo $correct_answers; ?></strong> /
                <?php echo $total_questions; ?>
            </p>
        </div>
    </div>
</body>

</html>