<?php
session_start();
include '../../config/config.php';

// Validate course_id
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch attendance description
$attendance_query = $conn->prepare("SELECT description FROM attendance WHERE course_id = ?");
$attendance_query->bind_param("i", $course_id);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result();
$description = $attendance_result->fetch_assoc()['description'] ?? 'Attendance for the course.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Attendance</h1>
        <form action="submit_attendance.php" method="POST">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" readonly><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Age</label>
                <input type="number" name="age" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Position/Designation</label>
                <input type="text" name="position_designation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Office/Affiliation</label>
                <input type="text" name="office_affiliation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email_address" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>