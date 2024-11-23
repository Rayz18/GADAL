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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['seminar_poster'])) {
    $seminar_title = $_POST['seminar_title'];
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
        $stmt->bind_param("issssssiii", $course_id, $seminar_title, $description, $date, $time, $venue, $poster_path, $include_registration, $include_attendance, $include_evaluation);

        if ($stmt->execute()) {
            $success_message = "Seminar details added successfully!";
        } else {
            $error_message = "Failed to add seminar details.";
        }
    } else {
        $error_message = "Failed to upload seminar poster.";
    }
}

// Fetch existing seminars for the selected course
$query = "SELECT * FROM seminars WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
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

        <div class="row">
            <!-- Add Seminar Form -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Add Seminar Details</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="seminar_title" class="form-label">Seminar Title</label>
                                <input type="text" name="seminar_title" class="form-control" required>
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

            <!-- Existing Seminars Section with Buttons -->
            <div class="col-md-6">
                <div class="card shadow-sm overflow-auto" style="max-height: 690px;">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Existing Seminar Details</h4>
                        <?php if ($result->num_rows > 0): ?>
                            <ul class="list-group seminar-list">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div style="flex-grow: 1;">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($row['seminar_title']); ?></h5>
                                            <p class="mb-1 seminar-description">
                                                <?php echo htmlspecialchars($row['description']); ?>
                                            </p>
                                            <?php if ($row['poster_path']): ?>
                                                <img src="<?php echo htmlspecialchars($row['poster_path']); ?>"
                                                    class="img-fluid mt-2 rounded" alt="Seminar Poster">
                                            <?php endif; ?>
                                            <small class="text-muted d-block mt-2">
                                                <?php echo htmlspecialchars($row['date']); ?>,
                                                <?php echo htmlspecialchars($row['time']); ?> at
                                                <?php echo htmlspecialchars($row['venue']); ?>
                                            </small>
                                            <!-- Conditionally display buttons -->
                                            <div class="mt-2">
                                                <?php if ($row['include_registration']): ?>
                                                    <a href="add_registration.php?seminar_id=<?php echo $row['seminar_id']; ?>"
                                                        class="btn btn-sm btn-success">Registration</a>
                                                <?php endif; ?>
                                                <?php if ($row['include_attendance']): ?>
                                                    <a href="add_attendance.php?seminar_id=<?php echo $row['seminar_id']; ?>"
                                                        class="btn btn-sm btn-info">Attendance</a>
                                                <?php endif; ?>
                                                <?php if ($row['include_evaluation']): ?>
                                                    <a href="add_evaluation.php?seminar_id=<?php echo $row['seminar_id']; ?>"
                                                        class="btn btn-sm btn-warning">Evaluation</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <a href="edit_seminar.php?seminar_id=<?php echo htmlspecialchars($row['seminar_id']); ?>"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="delete_seminar.php?seminar_id=<?php echo htmlspecialchars($row['seminar_id']); ?>&course_id=<?php echo htmlspecialchars($course_id); ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this seminar?');">Delete</a>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center text-muted">No seminars found for this course.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>