<?php
session_start();
require_once '../../config/config.php';

$selected_program_id = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $program_id = $_POST['program_id'];
    $course_name = $_POST['course_name'];
    $course_desc = $_POST['course_desc'];
    $course_date = $_POST['course_date'];
    $offered_mode = $_POST['offered_mode'];

    $enable_registration = isset($_POST['enable_registration']) ? 1 : 0;
    $enable_attendance = isset($_POST['enable_attendance']) ? 1 : 0;
    $enable_evaluation = isset($_POST['enable_evaluation']) ? 1 : 0;

    $course_img = '';
    if (!empty($_FILES['course_img']['name'])) {
        $target_dir = "../../staff/upload/";
        $target_file = $target_dir . basename($_FILES['course_img']['name']);
        if (move_uploaded_file($_FILES['course_img']['tmp_name'], $target_file)) {
            $course_img = $_FILES['course_img']['name'];
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses (program_id, course_name, course_img, course_desc, course_date, offered_mode, enable_registration, enable_attendance, enable_evaluation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssiii", $program_id, $course_name, $course_img, $course_desc, $course_date, $offered_mode, $enable_registration, $enable_attendance, $enable_evaluation);
    $stmt->execute();
    $stmt->close();

    header('Location: staff_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="text-center text-primary mb-4">Add Course</h2>
                        <form action="add_course.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <?php
                                $result = $conn->query("SELECT program_id, program_name FROM programs");
                                while ($row = $result->fetch_assoc()) {
                                    if ($row['program_id'] == $selected_program_id) {
                                        echo "<p class='form-control-plaintext fw-bold fs-5'>" . htmlspecialchars($row['program_name']) . "</p>";
                                        echo "<input type='hidden' name='program_id' value='" . $row['program_id'] . "'>";
                                    }
                                }
                                ?>
                            </div>
                            <div class="mb-3">
                                <label for="course_name" class="form-label">Course Name:</label>
                                <input type="text" id="course_name" name="course_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="course_desc" class="form-label">Course Description:</label>
                                <textarea id="course_desc" name="course_desc" class="form-control" rows="4"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="course_img" class="form-label">Course Image:</label>
                                <input type="file" id="course_img" name="course_img" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="course_date" class="form-label">Course Date:</label>
                                <input type="date" id="course_date" name="course_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="offered_mode" class="form-label">Offered Mode:</label>
                                <select id="offered_mode" name="offered_mode" class="form-control" required
                                    onchange="toggleFaceToFaceOptions(this.value)">
                                    <option value="online">Online</option>
                                    <option value="face_to_face">Face to Face</option>
                                </select>
                            </div>
                            <div id="face_to_face_options" class="mb-3">
                                <label class="form-label">Face to Face Options:</label>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_registration" name="enable_registration"
                                        class="form-check-input" disabled checked>
                                    <label for="enable_registration" class="form-check-label">Enable Registration
                                        (Predefined)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_attendance" name="enable_attendance"
                                        class="form-check-input" disabled checked>
                                    <label for="enable_attendance" class="form-check-label">Enable Attendance
                                        (Predefined)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="enable_evaluation" name="enable_evaluation"
                                        class="form-check-input" disabled checked>
                                    <label for="enable_evaluation" class="form-check-label">Enable Evaluation
                                        (Predefined)</label>
                                </div>
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