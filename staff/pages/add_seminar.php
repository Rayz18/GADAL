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

// Fetch the course title
$course_query = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course_title = '';
if ($course_result->num_rows > 0) {
    $course_row = $course_result->fetch_assoc();
    $course_title = $course_row['course_name'];
} else {
    $error_message = "Invalid course selected.";
}

// Check for existing seminar for the course
$existing_seminar_query = $conn->prepare("SELECT COUNT(*) as seminar_count FROM seminars WHERE course_id = ?");
$existing_seminar_query->bind_param("i", $course_id);
$existing_seminar_query->execute();
$seminar_count_result = $existing_seminar_query->get_result();
$seminar_count_row = $seminar_count_result->fetch_assoc();
$existing_seminar_count = $seminar_count_row['seminar_count'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['seminar_poster'])) {
    if ($existing_seminar_count > 0) {
        $error_message = "A seminar already exists for this course.";
    } else {
        $description = $_POST['description'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $venue = $_POST['venue'];

        // Checkboxes for optional buttons
        $include_registration = isset($_POST['include_registration']) ? 1 : 0;
        $include_attendance = isset($_POST['include_attendance']) ? 1 : 0;
        $include_evaluation = isset($_POST['include_evaluation']) ? 1 : 0;

        $file = $_FILES['seminar_poster'];
        $target_dir = "../../staff/upload/seminars/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $poster_name = time() . '_' . basename($file['name']);
        $target_file = $target_dir . $poster_name;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $poster_path = "staff/upload/seminars/" . $poster_name;
            $query = "INSERT INTO seminars (course_id, seminar_title, description, date, time, venue, poster_path, include_registration, include_attendance, include_evaluation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issssssiii", $course_id, $course_title, $description, $date, $time, $venue, $poster_path, $include_registration, $include_attendance, $include_evaluation);

            if ($stmt->execute()) {
                $success_message = "Seminar details added successfully!";
                $existing_seminar_count = 1; // Prevent further additions
            } else {
                $error_message = "Failed to add seminar details.";
            }
        } else {
            $error_message = "Failed to upload seminar poster.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Seminar Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../staff/assets/css/add_seminar.css">
</head>

<body class="d-flex flex-column vh-100">
    <div class="container-fluid flex-grow-1 overflow-auto">
        <!-- Back Button -->
        <a href="manage_programs.php" class="btn btn-outline-secondary back-button">&larr; Back</a>
        <div class="text-center mt-4">
            <h1 class="text-light-purple">Add Seminar Details</h1>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($existing_seminar_count == 0): ?>
            <div class="row">
                <!-- Add Seminar Form -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">Add Seminar Details</h4>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="seminar_title" class="form-label">Seminar Title</label>
                                    <input type="text" name="seminar_title" class="form-control"
                                        value="<?php echo htmlspecialchars($course_title); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label">Date</label>
                                        <input type="date" name="date" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="time" class="form-label">Time</label>
                                        <input type="time" name="time" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="seminar_poster" class="form-label">Poster</label>
                                        <input type="file" name="seminar_poster" class="form-control" required>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="venue" class="form-label">Venue</label>
                                    <input type="text" name="venue" class="form-control" required>
                                </div>
                                <!-- Optional Buttons -->
                                <div class="mb-3">
                                    <label class="form-label">Optional Buttons</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_registration" class="form-check-input"
                                            id="registrationCheckbox">
                                        <label class="form-check-label" for="registrationCheckbox">Add Registration
                                            Button</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_attendance" class="form-check-input"
                                            id="attendanceCheckbox">
                                        <label class="form-check-label" for="attendanceCheckbox">Add Attendance
                                            Button</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="include_evaluation" class="form-check-input"
                                            id="evaluationCheckbox">
                                        <label class="form-check-label" for="evaluationCheckbox">Add Evaluation
                                            Button</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Add Seminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">A seminar already exists for this course. You cannot add another seminar.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>