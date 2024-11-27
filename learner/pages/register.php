<?php
session_start();
include '../../config/config.php';

// Validate course_id
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$learner_id = $_SESSION['learner_id'] ?? 0;

// Check if registration is already completed
$registration_query = $conn->prepare("SELECT is_completed FROM registrations WHERE course_id = ? AND learner_id = ?");
$registration_query->bind_param("ii", $course_id, $learner_id);
$registration_query->execute();
$registration_result = $registration_query->get_result();
$registration = $registration_result->fetch_assoc();

if ($registration && $registration['is_completed'] == 1) {
    header("Location: success_page.php?message=Registration already completed!");
    exit();
}

// Fetch course details
$course_query = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();
$course_name = $course ? htmlspecialchars($course['course_name']) : "Unknown Course";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .description-box {
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="text-primary">Registration Form</h1>
            <h3><?php echo $course_name; ?></h3>
        </div>

        <div class="form-container mx-auto">
            <div class="description-box">
                <p>Dear Participants, <br> Please complete this registration form. We assure you that your responses
                    will be kept in strict confidentiality. Thank
                    you!</p>
            </div>

            <form action="submit_registration.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="ex. Dela Cruz, Juan, A." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Prefer not to say</option>
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

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>