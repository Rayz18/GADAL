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

// Add new question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $correct_option = $_POST['correct_option'];

    $query = "INSERT INTO pre_test_questions (course_id, question_text, option_a, option_b, option_c, correct_option) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $course_id, $question_text, $option_a, $option_b, $option_c, $correct_option);

    if ($stmt->execute()) {
        $success_message = "Pre-Test question added successfully!";
    } else {
        $error_message = "Failed to add Pre-Test question.";
    }
}

// Delete question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['pre_test_id'];

    $query = "DELETE FROM pre_test_questions WHERE pre_test_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        $success_message = "Pre-Test question deleted successfully!";
    } else {
        $error_message = "Failed to delete Pre-Test question.";
    }
}

// Fetch existing questions
$query = "SELECT * FROM pre_test_questions WHERE course_id = ?";
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
    <title>Pre-Test Questions</title>
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

        .messages {
            max-width: 600px;
            margin: 20px auto;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #FFFFFF;
        }

        .card-header {
            background-color: #9E8BB8 !important;
            color: #FFFFFF !important;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            font-size: 1.2rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .btn-primary {
            background-color: #9E8BB8;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #7C6A98;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #C96A6A;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-danger:hover {
            background-color: #A43A3A;
            transform: translateY(-2px);
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

        .table td {
            max-width: 200px;
            /* Adjust this value for better width management */
            word-wrap: break-word;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #F1EDF7;
        }

        .answer-badge {
            font-size: 0.9rem;
            font-weight: bold;
            color: #FFFFFF;
            background-color: #C96A6A;
            padding: 5px 10px;
            border-radius: 8px;
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

        .modal-header {
            background-color: #9E8BB8;
            color: #FFFFFF;
            border-bottom: none;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-footer {
            border-top: none;
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
<div class="layout">
        <!-- Sidebar -->
        <div id="toggle-sidebar" class="toggle-sidebar">
            <!-- Sidebar content can go here -->
        </div>
        <?php include '../../public/includes/StaffNavBar.php'; ?>
        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Toggle Sidebar Icon -->
            <div id="toggle-sidebar" class="toggle-sidebar"></div>
            <h1 class="page-title">MANAGE PRE-TEST QUESTIONS</h1>

        <!-- Success/Error Messages -->
        <div class="messages">
            <?php if ($success_message): ?>
                <div id="successMessage" class="alert alert-success text-center">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div id="errorMessage" class="alert alert-danger text-center">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4 text-end">
            <!-- Button to open Add Question Modal -->
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                + Add Question
            </button>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="card-header">Pre-Test Questions</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Option A</th>
                                <th>Option B</th>
                                <th>Option C</th>
                                <th>Correct Answer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                                    <td><?php echo htmlspecialchars($row['option_a']); ?></td>
                                    <td><?php echo htmlspecialchars($row['option_b']); ?></td>
                                    <td><?php echo htmlspecialchars($row['option_c']); ?></td>
                                    <td>
                                        <span
                                            class="answer-badge"><?php echo strtoupper(htmlspecialchars($row['correct_option'])); ?></span>
                                    </td>
                                    <td>
                                        <!-- Delete Question Button -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="delete_question" value="1">
                                            <input type="hidden" name="pre_test_id"
                                                value="<?php echo $row['pre_test_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuestionModalLabel">Add Pre-Test Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="add_question" value="1">
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question:</label>
                            <textarea name="question_text" class="form-control" placeholder="Enter the question"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="option_a" class="form-label">Option A:</label>
                            <input type="text" name="option_a" class="form-control" placeholder="Enter option A"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="option_b" class="form-label">Option B:</label>
                            <input type="text" name="option_b" class="form-control" placeholder="Enter option B"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="option_c" class="form-label">Option C:</label>
                            <input type="text" name="option_c" class="form-control" placeholder="Enter option C"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="correct_option" class="form-label">Correct Option (a/b/c):</label>
                            <input type="text" name="correct_option" class="form-control" maxlength="1"
                                placeholder="Enter the correct option (a/b/c)" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Question</button>
                    </form>
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
    <script>document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const toggleButton = document.getElementById("toggle-sidebar");

    toggleButton.addEventListener("click", function () {
        if (sidebar.classList.contains("open")) {
            // Close the sidebar
            sidebar.classList.remove("open");
            content.classList.remove("shifted");
        } else {
            // Open the sidebar
            sidebar.classList.add("open");
            content.classList.add("shifted");
        }
    });
});</script>
</body>

</html>