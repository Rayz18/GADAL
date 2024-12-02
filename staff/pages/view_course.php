<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

// Get the course_id from the URL
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    echo "Invalid course ID.";
    exit;
}

// Fetch course details
$course_query = $conn->query("
    SELECT courses.*, programs.program_name 
    FROM courses 
    INNER JOIN programs ON courses.program_id = programs.program_id 
    WHERE courses.course_id = '$course_id' AND courses.archive = TRUE
");
$course = $course_query->fetch_assoc();

if (!$course) {
    echo "Course not found or is not archived.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .course-details-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #4e73df;
            color: white;
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        .card-header img {
            width: 120px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: white;
            padding: 5px;
        }

        .course-title {
            margin-top: 15px;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .content-section {
            margin-bottom: 20px;
        }

        .content-section h5 {
            margin-bottom: 10px;
            color: #6c757d;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .content-section p {
            font-size: 1rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 20px 15px;
            }

            .card-header img {
                width: 100px;
            }

            .course-title {
                font-size: 1.5rem;
            }

            .content-section h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <div class="container mt-5">
        <div class="course-details-container">
            <!-- Course Details -->
            <div class="card">
                <div class="card-header">
                    <img src="../../staff/upload/<?php echo htmlspecialchars($course['course_img']); ?>"
                        alt="Course Poster">
                    <div class="course-title">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- General Information -->
                    <div class="content-section">
                        <h5>Program</h5>
                        <p><?php echo htmlspecialchars($course['program_name']); ?></p>
                    </div>

                    <!-- Course Description -->
                    <div class="content-section">
                        <h5>Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($course['course_desc'])); ?></p>
                    </div>

                    <!-- Mode of Offering -->
                    <div class="content-section">
                        <h5>Mode of Offering</h5>
                        <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $course['offered_mode']))); ?></p>
                    </div>

                    <!-- Additional Details -->
                    <div class="content-section">
                        <h5>Additional Information</h5>
                        <?php if ($course['offered_mode'] === 'face_to_face') { ?>
                            <p><strong>Date:</strong>
                                <?php echo htmlspecialchars(date('F d, Y', strtotime($course['course_date']))); ?>
                            </p>
                        <?php } else { ?>
                            <p><strong>Start Date:</strong>
                                <?php echo htmlspecialchars(date('F d, Y', strtotime($course['start_date']))); ?>
                            </p>
                            <p><strong>End Date:</strong>
                                <?php echo htmlspecialchars(date('F d, Y', strtotime($course['end_date']))); ?>
                            </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>