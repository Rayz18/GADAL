<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Get course ID from query string
if (!isset($_GET['course_id'])) {
    die('Course ID is required!');
}

$course_id = intval($_GET['course_id']);

// Fetch course details
$course_query = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();

if ($course_result->num_rows === 0) {
    die('Course not found!');
}

$course = $course_result->fetch_assoc();

// Update course details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_desc = trim($_POST['course_desc']);
    $course_date = $_POST['course_date'];
    $offered_mode = $_POST['offered_mode'];

    $enable_registration = isset($_POST['enable_registration']) ? 1 : 0;
    $enable_attendance = isset($_POST['enable_attendance']) ? 1 : 0;
    $enable_evaluation = isset($_POST['enable_evaluation']) ? 1 : 0;

    $course_img = $course['course_img'];
    if (!empty($_FILES['course_img']['name'])) {
        $target_dir = "../../staff/upload/";
        $target_file = $target_dir . basename($_FILES['course_img']['name']);
        if (move_uploaded_file($_FILES['course_img']['tmp_name'], $target_file)) {
            $course_img = $_FILES['course_img']['name'];
        }
    }

    $update_query = $conn->prepare("UPDATE courses SET course_name = ?, course_desc = ?, course_date = ?, offered_mode = ?, enable_registration = ?, enable_attendance = ?, enable_evaluation = ?, course_img = ? WHERE course_id = ?");
    $update_query->bind_param("ssssiiisi", $course_name, $course_desc, $course_date, $offered_mode, $enable_registration, $enable_attendance, $enable_evaluation, $course_img, $course_id);

    if ($update_query->execute()) {
        $success_message = "Course updated successfully!";
    } else {
        $error_message = "Failed to update course. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="text-center text-primary mb-4">Edit Course</h2>
                        <?php if (!empty($error_message)) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>
                        <?php if (!empty($success_message)) { ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php } ?>
                        <form action="edit_course.php?course_id=<?php echo $course_id; ?>" method="POST"
                            enctype="multipart/form-data">
                            <div class="mb-3">
                                <p class="form-control-plaintext fw-bold fs-5">
                                    <?php echo htmlspecialchars($course['course_name']); ?></p>
                            </div>
                            <div class="mb-3">
                                <label for="course_name" class="form-label">Course Name:</label>
                                <input type="text" id="course_name" name="course_name" class="form-control"
                                    value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="course_desc" class="form-label">Course Description:</label>
                                <textarea id="course_desc" name="course_desc" class="form-control" rows="4"
                                    required><?php echo htmlspecialchars($course['course_desc']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="course_img" class="form-label">Course Image:</label>
                                <input type="file" id="course_img" name="course_img" class="form-control">
                                <?php if (!empty($course['course_img'])) { ?>
                                    <img src="../../staff/upload/<?php echo htmlspecialchars($course['course_img']); ?>"
                                        alt="Course Image" class="img-thumbnail mt-2" style="max-height: 150px;">
                                <?php } ?>
                            </div>
                            <div class="mb-3">
                                <label for="course_date" class="form-label">Course Date:</label>
                                <input type="date" id="course_date" name="course_date" class="form-control"
                                    value="<?php echo htmlspecialchars($course['course_date']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="offered_mode" class="form-label">Offered Mode:</label>
                                <select id="offered_mode" name="offered_mode" class="form-control" required
                                    onchange="toggleFaceToFaceOptions(this.value)">
                                    <option value="online" <?php echo $course['offered_mode'] == 'online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="face_to_face" <?php echo $course['offered_mode'] == 'face_to_face' ? 'selected' : ''; ?>>Face to Face</option>
                                </select>
                            </div>
                            <div id="face_to_face_options"
                                class="mb-3 <?php echo $course['offered_mode'] == 'face_to_face' ? '' : 'd-none'; ?>">
                                <label class="form-label">Face to Face Options:</label>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_registration" name="enable_registration"
                                        class="form-check-input" <?php echo $course['enable_registration'] ? 'checked' : ''; ?>>
                                    <label for="enable_registration" class="form-check-label">Enable
                                        Registration</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_attendance" name="enable_attendance"
                                        class="form-check-input" <?php echo $course['enable_attendance'] ? 'checked' : ''; ?>>
                                    <label for="enable_attendance" class="form-check-label">Enable Attendance</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_evaluation" name="enable_evaluation"
                                        class="form-check-input" <?php echo $course['enable_evaluation'] ? 'checked' : ''; ?>>
                                    <label for="enable_evaluation" class="form-check-label">Enable Evaluation</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="manage_programs.php" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFaceToFaceOptions(value) {
            const faceToFaceOptions = document.getElementById('face_to_face_options');
            faceToFaceOptions.classList.toggle('d-none', value !== 'face_to_face');
        }
    </script>
</body>

</html>