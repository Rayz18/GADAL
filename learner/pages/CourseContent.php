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
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .content-area {
            padding: 20px;
            background-color: #f9f9f9;
            min-height: 100vh;
            margin-top: 70px;
        }

        .course-header {
            padding: 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .course-header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .tab-content {
            background: #ffffff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }

        .dropdown-toggle {
            width: 100%;
            text-align: left;
            position: relative;
            padding-right: 25px;
        }

        .dropdown-toggle::after {
            content: '\25BC';
            font-size: 14px;
            color: #000;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }

        .dropdown-toggle[aria-expanded="true"]::after {
            content: '\25B2';
        }
    </style>
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>
    <?php include '../../public/includes/Sidebar.php'; ?>

    <!-- Sidebar and Content -->
    <div class="d-flex">
        <!-- Main Content Area -->
        <div class="content-area flex-grow-1">
            <div class="course-header">
                <h1><?php echo htmlspecialchars($course_name); ?></h1>
            </div>
            <div id="content-section" class="tab-content">
                <!-- JavaScript -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    const courseData = {
                        pre_test_questions: <?php echo json_encode($pre_test_questions_array); ?>,
                        post_test_questions: <?php echo json_encode($post_test_questions_array); ?>,
                        learning_materials: <?php echo json_encode($learning_materials); ?>
                    };

                    const contentData = {
                        'pre-test': `
                            <h2 class="text-secondary">Pre-Test</h2>
                            <form method="POST" action="submit_pretest.php">
                                <ol>
                                    ${courseData.pre_test_questions.map(q => `
                                        <li>
                                            ${q.question_text}
                                            <ul>
                                                <li><input type="radio" name="q${q.id}" value="a"> ${q.option_a}</li>
                                                <li><input type="radio" name="q${q.id}" value="b"> ${q.option_b}</li>
                                                <li><input type="radio" name="q${q.id}" value="c"> ${q.option_c}</li>
                                            </ul>
                                        </li>
                                    `).join('')}
                                </ol>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        `,
                        'learning-materials': `
                            <h2 class="text-secondary">Learning Materials</h2>
                            <div id="materials">
                                ${courseData.learning_materials.map(material => `
                                    <div class="content-section mb-4">
                                        <h5>${material.module_title}</h5>
                                        <p>${material.module_description}</p>
                                        ${material.video_url ? `<video controls style="max-width: 100%;"><source src="${material.video_url}" type="video/mp4">Your browser does not support video playback.</video>` : ''}
                                        ${material.pdf_url ? `<a href="${material.pdf_url}" target="_blank" class="btn btn-info mt-2">Download PDF</a>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        `,
                        'post-test': `
                            <h2 class="text-secondary">Post-Test</h2>
                            <form method="POST" action="submit_posttest.php">
                                <ol>
                                    ${courseData.post_test_questions.map(q => `
                                        <li>
                                            ${q.question_text}
                                            <ul>
                                                <li><input type="radio" name="q${q.id}" value="a"> ${q.option_a}</li>
                                                <li><input type="radio" name="q${q.id}" value="b"> ${q.option_b}</li>
                                                <li><input type="radio" name="q${q.id}" value="c"> ${q.option_c}</li>
                                            </ul>
                                        </li>
                                    `).join('')}
                                </ol>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        `,
                    };

                    document.addEventListener("DOMContentLoaded", function () {
                        const urlParams = new URLSearchParams(window.location.search);
                        const tab = urlParams.get('tab') || 'pre-test';
                        const content = contentData[tab] || "<p>No content available.</p>";
                        document.getElementById('content-section').innerHTML = content;
                    });
                </script>
            </div>
        </div>
    </div>
</body>

</html>