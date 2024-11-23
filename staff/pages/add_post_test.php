<?php
include '../../config/config.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$course_id = $_GET['course_id'] ?? null;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $correct_option = $_POST['correct_option'];

    $query = "INSERT INTO post_test_questions (course_id, question_text, option_a, option_b, option_c, correct_option) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $course_id, $question_text, $option_a, $option_b, $option_c, $correct_option);

    if ($stmt->execute()) {
        $success_message = "Post-Test question added successfully!";
    } else {
        $error_message = "Failed to add Post-Test question.";
    }
}

// Fetch existing questions
$query = "SELECT * FROM post_test_questions WHERE course_id = ?";
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
    <title>Post-Test Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/add_post_test.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <!-- Sidebar -->
    <div class="sidebar collapsed"></div>

    <!-- Sidebar Toggle Button -->
    <div id="toggle-sidebar" class="toggle-sidebar"></div>

    <!-- Main Content -->
    <div id="content" class="container-fluid">
        <h1 class="post-page-title">POST-TEST QUESTIONS</h1>

        <!-- Success/Error Messages -->
        <div class="post-messages">
            <?php if ($success_message): ?>
                <div id="successMessage" class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div id="errorMessage" class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-5">
            <!-- Add Question Form -->
            <div class="col-lg-6">
                <div class="card post-form-card">
                    <div class="card-header post-form-header">Add Post-Test Question</div>
                    <div class="card-body post-form-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="question_text" class="form-label fw-bold">Question:</label>
                                <input type="text" name="question_text" class="form-control"
                                    placeholder="Enter the question" required>
                            </div>
                            <div class="mb-3">
                                <label for="option_a" class="form-label fw-bold">Option A:</label>
                                <input type="text" name="option_a" class="form-control" placeholder="Enter option A"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="option_b" class="form-label fw-bold">Option B:</label>
                                <input type="text" name="option_b" class="form-control" placeholder="Enter option B"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="option_c" class="form-label fw-bold">Option C:</label>
                                <input type="text" name="option_c" class="form-control" placeholder="Enter option C"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="correct_option" class="form-label fw-bold">Correct Option (a/b/c):</label>
                                <input type="text" name="correct_option" class="form-control" maxlength="1"
                                    placeholder="Enter the correct option (a/b/c)" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Question</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Questions -->
            <div class="col-lg-6">
                <div class="card post-existing-card">
                    <div class="card-header post-existing-header">Existing Post-Test Questions</div>
                    <div class="card-body post-existing-body">
                        <table class="table table-striped table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th class="post-question-column">Question</th>
                                    <th class="post-answer-column">Answer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="post-question-text">
                                            <?php echo htmlspecialchars($row['question_text']); ?>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="post-answer-badge"><?php echo strtoupper(htmlspecialchars($row['correct_option'])); ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 3000);
    </script>
</body>

</html>