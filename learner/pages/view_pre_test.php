<?php
include '../../config/config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['learner_logged_in'])) {
    header('Location: learner_login.php');
    exit();
}

$course_id = $_GET['course_id'] ?? null;

// Fetch pre-test questions for the learner
$query = "SELECT question_text, option_a, option_b, option_c FROM pre_test_questions WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F7F4FA;
            color: #4A4A4A;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: bold;
            color: #9E8BB8;
            text-align: center;
            margin: 30px 0;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            white-space: normal;
        }

        .table th {
            background-color: #9E8BB8;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: bold;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #F1EDF7;
        }

        .form-label {
            font-weight: bold;
            color: #9E8BB8;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #D6CCE3;
        }

        .form-control:focus {
            border-color: #9E8BB8;
            box-shadow: 0 0 5px rgba(158, 139, 184, 0.5);
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <div class="container mt-4">
        <h1 class="page-title">Pre-Test</h1>

        <form method="POST" action="submit_pre_test.php">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Your Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo $counter++; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['question_text']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['option_a']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['option_b']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['option_c']); ?>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?php echo $counter; ?>]"
                                            value="a" required> A
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?php echo $counter; ?>]"
                                            value="b"> B
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?php echo $counter; ?>]"
                                            value="c"> C
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4 py-2">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>