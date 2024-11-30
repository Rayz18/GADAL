<?php
session_start();
include '../../config/config.php';

// Check if learner is logged in
if (!isset($_SESSION['learner_id'])) {
    header('Location: login.php');
    exit;
}

$learner_id = $_SESSION['learner_id'];
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

// Check if the learner has already completed the pre-test
$query = "SELECT * FROM pre_test_results WHERE learner_id = ? AND course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $learner_id, $course_id);
$stmt->execute();
$pre_test_result = $stmt->get_result()->fetch_assoc();

// Fetch approved Pre-Test Questions
$query = "SELECT * FROM pre_test_questions WHERE course_id = ? AND status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$pre_test_questions = $stmt->get_result();

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
                <?php
                // Load content based on the selected tab
                $tab = $_GET['tab'] ?? 'pre-test';

                if ($tab === 'pre-test'): ?>
                    <h2 class="text-secondary">Pre-Test</h2>
                    <?php if ($pre_test_result): ?>
                        <!-- Show Pre-Test Results if already completed -->
                        <div class="text-center">
                            <h1 class="mb-4">Pre-Test Results</h1>
                            <p class="lead">Your performance:</p>
                            <h2 class="display-4 text-success"><?php echo htmlspecialchars($pre_test_result['score']); ?>%</h2>
                            <p class="lead">Correct Answers:
                                <strong><?php echo htmlspecialchars($pre_test_result['correct_answers']); ?></strong> /
                                <?php echo htmlspecialchars($pre_test_result['total_questions']); ?>
                            </p>
                            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=learning-materials"
                                class="btn btn-primary mt-4">
                                Proceed to Learning Materials
                            </a>
                        </div>
                    <?php elseif ($pre_test_questions->num_rows > 0): ?>
                        <!-- Show Pre-Test Questions if not yet completed -->
                        <form method="POST" action="submit_pre_test.php" style="max-width: 800px; margin: auto;">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <p class="text-muted text-center mb-4">"Before you begin the course, please complete this pre-test
                                to assess your current knowledge on the topic."</p>
                            <?php
                            $counter = 1;
                            while ($row = $pre_test_questions->fetch_assoc()): ?>
                                <div class="mb-4 d-flex align-items-start question-container">
                                    <span class="question-counter"><?php echo $counter; ?>.</span>
                                    <div>
                                        <p class="question-text"><?php echo htmlspecialchars($row['question_text']); ?></p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['pre_test_id']; ?>]" value="a" required>
                                            <label class="form-check-label">
                                                a.) <?php echo htmlspecialchars($row['option_a']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['pre_test_id']; ?>]" value="b">
                                            <label class="form-check-label">
                                                b.) <?php echo htmlspecialchars($row['option_b']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['pre_test_id']; ?>]" value="c">
                                            <label class="form-check-label">
                                                c.) <?php echo htmlspecialchars($row['option_c']); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $counter++;
                            endwhile; ?>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2"
                                    style="background-color: #C7A1D4; border: none; border-radius: 6px;">
                                    Submit
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-center text-muted">No Pre-Test questions available at the moment.</p>
                    <?php endif; ?>
                <?php elseif ($tab === 'learning-materials'): ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>