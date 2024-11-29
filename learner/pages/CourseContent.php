<?php
session_start();
include '../../config/config.php';

// Check if learner is logged in
if (!isset($_SESSION['learner_id'])) {
    header('Location: login.php');
    exit;
}

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    echo "Invalid Course ID.";
    exit;
}

// Fetch approved course details
$query = "SELECT * FROM courses WHERE course_id = ? AND status = 'approved'";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Failed to prepare statement: " . $conn->error;
    exit;
}
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

// Ensure the course name always has a value
$course_name = $course['course_name'] ?? 'Course Interface';

// Fetch approved Pre-Test Questions
$query = "SELECT * FROM pre_test_questions WHERE course_id = ? AND status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$pre_test_questions = $stmt->get_result();

// Fetch approved Post-Test Questions
$query = "SELECT * FROM post_test_questions WHERE course_id = ? AND status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$post_test_questions = $stmt->get_result();

// Fetch learning materials for the course
$query = "SELECT * FROM learning_materials WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$materials_result = $stmt->get_result();
$learning_materials = [];
while ($row = $materials_result->fetch_assoc()) {
    $learning_materials[] = $row;
}

// Convert result sets to arrays
$pre_test_questions_array = [];
while ($row = $pre_test_questions->fetch_assoc()) {
    $pre_test_questions_array[] = $row;
}

$post_test_questions_array = [];
while ($row = $post_test_questions->fetch_assoc()) {
    $post_test_questions_array[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course_name); ?> - Course Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="../../public/assets/css/Sidebar.css">
    <script src="../../public/assets/js/sidebarToggle.js" defer></script>
    <link rel="stylesheet" href="../../learner/assets/css/CourseContent.css">
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>
    <?php include '../../public/includes/Sidebar.php'; ?>

    <div class="d-flex">
        <div class="content-area flex-grow-1">
            <div class="course-header">
                <h1><?php echo htmlspecialchars($course_name); ?></h1>
            </div>

            <div id="content-section">
                <h2 class="text-secondary">Learning Materials</h2>
                <div class="accordion" id="materialsAccordion">
                    <?php foreach ($learning_materials as $index => $material): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false"
                                    aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($material['module_title']); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#materialsAccordion">
                                <div class="accordion-body">
                                    <p><?php echo htmlspecialchars($material['module_discussion']); ?></p>

                                    <!-- Render video only if it exists -->
                                    <?php if (!empty($material['video_url'])): ?>
                                        <div class="video-container mb-3">
                                            <div class="video-title text-center">
                                                <strong><?php echo htmlspecialchars($material['video_title'] ?? 'Video'); ?></strong>
                                            </div>
                                            <video controls>
                                                <source src="<?php echo htmlspecialchars($material['video_url']); ?>"
                                                    type="video/mp4">
                                                Your browser does not support video playback.
                                            </video>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Render PDF link only if it exists -->
                                    <?php if (!empty($material['pdf_url'])): ?>
                                        <div class="pdf-container mb-3">
                                            <a href="<?php echo htmlspecialchars($material['pdf_url']); ?>" target="_blank"
                                                class="btn pdf-button">
                                                <?php echo htmlspecialchars($material['pdf_title'] ?? 'PDF File'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>