<?php
include '../../config/config.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$seminar_id = $_GET['seminar_id'];
$success_message = '';
$error_message = '';

// Fetch existing seminar data for editing
$query = "SELECT * FROM seminars WHERE seminar_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seminar_id);
$stmt->execute();
$seminar = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update seminar details
    $seminar_title = $_POST['seminar_title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $venue = $_POST['venue'];

    // Optional buttons
    $include_registration = isset($_POST['include_registration']) ? 1 : 0;
    $include_attendance = isset($_POST['include_attendance']) ? 1 : 0;
    $include_evaluation = isset($_POST['include_evaluation']) ? 1 : 0;

    $query = "UPDATE seminars SET seminar_title = ?, description = ?, date = ?, time = ?, venue = ?, include_registration = ?, include_attendance = ?, include_evaluation = ? WHERE seminar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssiiii", $seminar_title, $description, $date, $time, $venue, $include_registration, $include_attendance, $include_evaluation, $seminar_id);


    if ($stmt->execute()) {
        $success_message = "Seminar details updated successfully!";
    } else {
        $error_message = "Failed to update seminar details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Seminar Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../staff/assets/css/add_seminar.css"> <!-- Using the same CSS as add_seminar -->
</head>

<body class="d-flex flex-column vh-100">
    <div class="container-fluid flex-grow-1 overflow-auto">
        <div class="text-center mt-4">
            <h1 class="text-light-purple">Edit Seminar Details</h1>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Edit Seminar Details</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="seminar_title" class="form-label">Seminar Title</label>
                                <input type="text" name="seminar_title" class="form-control"
                                    value="<?php echo htmlspecialchars($seminar['seminar_title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"
                                    required><?php echo htmlspecialchars($seminar['description']); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control"
                                        value="<?php echo htmlspecialchars($seminar['date']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="time" class="form-label">Time</label>
                                    <input type="time" name="time" class="form-control"
                                        value="<?php echo htmlspecialchars($seminar['time']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="venue" class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control"
                                    value="<?php echo htmlspecialchars($seminar['venue']); ?>" required>
                            </div>
                            <!-- Optional Buttons -->
                            <div class="mb-3">
                                <label class="form-label">Optional Buttons</label>
                                <div class="form-check">
                                    <input type="checkbox" name="include_registration" class="form-check-input" <?php echo $seminar['include_registration'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Add Registration Button</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="include_attendance" class="form-check-input" <?php echo $seminar['include_attendance'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Add Attendance Button</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="include_evaluation" class="form-check-input" <?php echo $seminar['include_evaluation'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Add Evaluation Button</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Seminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>