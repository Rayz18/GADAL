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
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) {
    echo "Course not found or not approved.";
    exit;
}

// Fetch approved Introduction Content
$query = "SELECT * FROM course_sections WHERE course_id = ? AND section_name = 'Introduction' AND status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course_section = $stmt->get_result()->fetch_assoc();

// Fetch approved Pre-Test Questions
$query = "SELECT * FROM pre_test_questions WHERE course_id = ? AND status = 'approved'";
$stmt->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$pre_test_questions = $stmt->get_result();

// Fetch approved Learning Materials
$query = "SELECT * FROM learning_materials WHERE course_id = ? AND status = 'approved'";
$stmt->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$learning_materials = $stmt->get_result();

// Fetch approved Videos
$query = "SELECT * FROM course_videos WHERE course_id = ? AND status = 'approved'";
$stmt->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$videos = $stmt->get_result();

// Fetch approved Post-Test Questions
$query = "SELECT * FROM post_test_questions WHERE course_id = ? AND status = 'approved'";
$stmt->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$post_test_questions = $stmt->get_result();

// Convert result sets to arrays
$pre_test_questions_array = [];
while ($row = $pre_test_questions->fetch_assoc()) {
    $pre_test_questions_array[] = $row;
}

$learning_materials_array = [];
while ($material = $learning_materials->fetch_assoc()) {
    $learning_materials_array[] = $material;
}

$videos_array = [];
while ($video = $videos->fetch_assoc()) {
    $videos_array[] = $video;
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
    <title><?php echo htmlspecialchars($course['course_name']); ?> - Course Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="../../learner/assets/css/CourseContent.css">
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <div class="container mt-4">
        <h1 class="text-primary text-center mb-4"><?php echo htmlspecialchars($course['course_name']); ?></h1>
        <div id="content-section"></div>
    </div>

    <!-- Pass PHP data as JSON -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const courseData = {
            course_section: <?php echo json_encode($course_section); ?>,
            pre_test_questions: <?php echo json_encode($pre_test_questions_array); ?>,
            learning_materials: <?php echo json_encode($learning_materials_array); ?>,
            videos: <?php echo json_encode($videos_array); ?>,
            post_test_questions: <?php echo json_encode($post_test_questions_array); ?>,
        };

        const contentData = {
            introduction: `
                <div class="mb-4">
                    <h2 class="text-secondary">Introduction</h2>
                    <p>${courseData.course_section && courseData.course_section.section_content
                    ? courseData.course_section.section_content.replace(/\n/g, '<br>')
                    : 'No introduction available for this course.'}</p>
                </div>
            `,
            'pre-test': `
                <div class="mb-4">
                    <h2 class="text-secondary">Pre-Test</h2>
                    <p>Please take this pre-test to assess your prior knowledge before starting the course.</p>
                    <form method="POST" action="submit_pretest.php" class="mt-3">
                        <ol>
                            ${courseData.pre_test_questions.map(q => `
                                <li class="mb-2">
                                    ${q.question_text}
                                    <ul class="list-unstyled">
                                        <li><label><input type="radio" name="q${q.id}" value="a" class="me-2"> a) ${q.option_a}</label></li>
                                        <li><label><input type="radio" name="q${q.id}" value="b" class="me-2"> b) ${q.option_b}</label></li>
                                        <li><label><input type="radio" name="q${q.id}" value="c" class="me-2"> c) ${q.option_c}</label></li>
                                    </ul>
                                </li>
                            `).join('')}
                        </ol>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            `,
            'learning-materials': `
                <div class="mb-4">
                    <h2 class="text-secondary">Learning Materials</h2>
                    <ul class="list-group">
                        ${courseData.learning_materials.map(material => `
                            <li class="list-group-item"><a href="${material.file_path}" target="_blank" class="text-decoration-none text-primary">${material.file_name}</a></li>
                        `).join('')}
                    </ul>
                </div>
            `,
            videos: `
                <div class="mb-4">
                    <h2 class="text-secondary">Videos</h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        ${courseData.videos.map(video => `
                            <div class="col">
                                <div class="card shadow-sm">
                                    <video class="card-img-top" controls>
                                        <source src="${video.video_path}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `,
            'post-test': `
                <div class="mb-4">
                    <h2 class="text-secondary">Post-Test</h2>
                    <p>Take the post-test to evaluate your understanding after completing the course.</p>
                    <form method="POST" action="submit_posttest.php" class="mt-3">
                        <ol>
                            ${courseData.post_test_questions.map(q => `
                                <li class="mb-2">
                                    ${q.question_text}
                                    <ul class="list-unstyled">
                                        <li><label><input type="radio" name="q${q.id}" value="a" class="me-2"> a) ${q.option_a}</label></li>
                                        <li><label><input type="radio" name="q${q.id}" value="b" class="me-2"> b) ${q.option_b}</label></li>
                                        <li><label><input type="radio" name="q${q.id}" value="c" class="me-2"> c) ${q.option_c}</label></li>
                                    </ul>
                                </li>
                            `).join('')}
                        </ol>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            `,
        };

        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'introduction';

            const contentSection = document.getElementById('content-section');
            contentSection.innerHTML = contentData[tab] || "<p>No content available.</p>";
        });
    </script>
</body>

</html>