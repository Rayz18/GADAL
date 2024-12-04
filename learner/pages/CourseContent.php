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

// Check if the learner has already completed the post-test
$query = "SELECT * FROM post_test_results WHERE learner_id = ? AND course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $learner_id, $course_id);
$stmt->execute();
$post_test_result = $stmt->get_result()->fetch_assoc();

// Fetch approved Post-Test Questions
$query = "SELECT * FROM post_test_questions WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$post_test_questions = $stmt->get_result();

// Check if the learner has already completed the quiz
$query = "SELECT * FROM quiz_results WHERE learner_id = ? AND course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $learner_id, $course_id);
$stmt->execute();
$quiz_result = $stmt->get_result()->fetch_assoc();

// Fetch approved Quiz Questions
$query = "SELECT * FROM quiz_questions WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$quiz_questions = $stmt->get_result();

// Fetch learning materials for the course
$learning_materials = [];
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
    <script src="../../public/assets/js/syncSidebar.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                $selected_module_id = $_GET['module'] ?? null;

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
                <?php elseif ($tab === 'post-test'): ?>
                    <h2 class="text-secondary">Post-Test</h2>
                    <?php if ($post_test_result): ?>
                        <!-- Show Post-Test Results if already completed -->
                        <div class="text-center">
                            <h1 class="mb-4">Post-Test Results</h1>
                            <p class="lead">Your performance:</p>
                            <h2 class="display-4 text-success"><?php echo htmlspecialchars($post_test_result['score']); ?>%</h2>
                            <p class="lead">Correct Answers:
                                <strong><?php echo htmlspecialchars($post_test_result['correct_answers']); ?></strong> /
                                <?php echo htmlspecialchars($post_test_result['total_questions']); ?>
                            </p>
                        </div>
                    <?php elseif ($post_test_questions->num_rows > 0): ?>
                        <!-- Show Post-Test Questions if not yet completed -->
                        <form method="POST" action="submit_post_test.php" style="max-width: 800px; margin: auto;">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <p class="text-muted text-center mb-4">"Now that you’ve completed the course, take this post-test to
                                evaluate what you’ve learned."</p>
                            <?php
                            $counter = 1;
                            while ($row = $post_test_questions->fetch_assoc()): ?>
                                <div class="mb-4 d-flex align-items-start question-container">
                                    <span class="question-counter"><?php echo $counter; ?>.</span>
                                    <div>
                                        <p class="question-text"><?php echo htmlspecialchars($row['question_text']); ?></p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['post_test_id']; ?>]" value="a" required>
                                            <label class="form-check-label">
                                                a.) <?php echo htmlspecialchars($row['option_a']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['post_test_id']; ?>]" value="b">
                                            <label class="form-check-label">
                                                b.) <?php echo htmlspecialchars($row['option_b']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['post_test_id']; ?>]" value="c">
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
                        <p class="text-center text-muted">No Post-Test questions available at the moment.</p>
                    <?php endif; ?>
                <?php elseif ($tab === 'quiz'): ?>
                    <h2 class="text-secondary">Quiz</h2>
                    <?php if ($quiz_result): ?>
                        <!-- Show Quiz Results if already completed -->
                        <div class="text-center">
                            <h1 class="mb-4">Quiz Results</h1>
                            <p class="lead">Your performance:</p>
                            <h2 class="display-4 text-success"><?php echo htmlspecialchars($quiz_result['score']); ?>%</h2>
                            <p class="lead">Correct Answers:
                                <strong><?php echo htmlspecialchars($quiz_result['correct_answers']); ?></strong> /
                                <?php echo htmlspecialchars($quiz_result['total_questions']); ?>
                            </p>
                        </div>
                    <?php elseif ($quiz_questions->num_rows > 0): ?>
                        <!-- Show Quiz Questions if not yet completed -->
                        <form method="POST" action="submit_quiz.php" style="max-width: 800px; margin: auto;">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <p class="text-muted text-center mb-4">"Complete this quiz to test your understanding."</p>
                            <?php
                            $counter = 1;
                            while ($row = $quiz_questions->fetch_assoc()): ?>
                                <div class="mb-4 d-flex align-items-start question-container">
                                    <span class="question-counter"><?php echo $counter; ?>.</span>
                                    <div>
                                        <p class="question-text"><?php echo htmlspecialchars($row['question_text']); ?></p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['quiz_id']; ?>]" value="a" required>
                                            <label class="form-check-label">
                                                a.) <?php echo htmlspecialchars($row['option_a']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['quiz_id']; ?>]" value="b">
                                            <label class="form-check-label">
                                                b.) <?php echo htmlspecialchars($row['option_b']); ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="answers[<?php echo $row['quiz_id']; ?>]" value="c">
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
                        <p class="text-center text-muted">No Quiz questions available at the moment.</p>
                    <?php endif; ?>
                <?php elseif ($tab === 'learning-materials'): ?>
                    <?php if (!$pre_test_result): ?>
                        <!-- Redirect learners to the Pre-Test if they haven't completed it -->
                        <script>
                            alert("You must complete the Pre-Test before accessing the Learning Materials.");
                            window.location.href = "CourseContent.php?course_id=<?php echo $course_id; ?>&tab=pre-test";
                        </script>
                    <?php else: ?>
                        <h2 class="text-secondary">Learning Materials</h2>
                        <div class="accordion" id="materialsAccordion">
                            <?php foreach ($learning_materials as $index => $material): ?>
                                <?php
                                // Determine if the accordion item should be expanded
                                $is_expanded = (isset($_GET['module']) && $_GET['module'] == $index);
                                // Check if the module has been marked as done by this user
                                $is_done = isset($_SESSION['completed_modules'][$index]) ? $_SESSION['completed_modules'][$index] : false;
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                        <button class="accordion-button <?php echo $is_expanded ? '' : 'collapsed'; ?>"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>"
                                            aria-expanded="<?php echo $is_expanded ? 'true' : 'false'; ?>"
                                            aria-controls="collapse<?php echo $index; ?>">
                                            <?php echo htmlspecialchars($material['module_title']); ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $index; ?>"
                                        class="accordion-collapse collapse <?php echo $is_expanded ? 'show' : ''; ?>"
                                        aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#materialsAccordion">
                                        <div class="accordion-body">
                                            <p class="preserved-text">
                                                <?php echo nl2br(htmlspecialchars($material['module_discussion'])); ?>
                                            </p>

                                            <?php if (!empty($material['video_url'])): ?>
                                                <div class="video-container mt-3">
                                                    <h5 class="video-title text-center">
                                                        <?php echo htmlspecialchars($material['video_title'] ?? 'Video'); ?>
                                                    </h5>
                                                    <video controls>
                                                        <source src="<?php echo htmlspecialchars($material['video_url']); ?>"
                                                            type="video/mp4">
                                                        Your browser does not support video playback.
                                                    </video>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($material['pdf_url'])): ?>
                                                <div class="file-container mt-3">
                                                    <a href="<?php echo htmlspecialchars($material['pdf_url']); ?>" target="_blank"
                                                        class="btn btn-secondary">
                                                        <?php echo htmlspecialchars($material['pdf_title'] ?? 'PDF File'); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Mark as Done Button placed to the right -->
                                            <div class="d-flex justify-content-end mt-3">
                                                <?php if (!$is_done): ?>
                                                    <form action="mark_done.php" method="POST">
                                                        <input type="hidden" name="module_index" value="<?php echo $index; ?>">
                                                        <button type="submit" class="btn btn-success">Mark as Done</button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-success" disabled>Marked as Done</button>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to load content dynamically
        function loadContent(tab, module = null) {
            $.ajax({
                url: "CourseContent.php",
                type: "GET",
                data: {
                    course_id: "<?php echo $course_id; ?>",
                    tab: tab,
                    module: module
                },
                success: function (response) {
                    const newContent = $(response).find('#content-section').html();
                    $('#content-section').html(newContent);

                    // Update active state in sidebar
                    $('.menu-item').removeClass('active');
                    if (module !== null) {
                        $(`a[href$="&tab=${tab}&module=${module}"]`).addClass('active');
                    } else {
                        $(`a[href$="&tab=${tab}"]`).addClass('active');
                    }
                },
                error: function () {
                    alert("Failed to load content.");
                }
            });
        }

        // Function to highlight the active sidebar item based on the clicked module
        function updateSidebarActiveState(tab, module = null) {
            // Remove 'active' class from all sidebar items
            $('.menu-item').removeClass('active');
            $('.submenu .menu-item').removeClass('active');

            // Highlight the relevant item
            if (module !== null) {
                $(`a[href$="&tab=${tab}&module=${module}"]`).addClass('active');
            } else {
                $(`a[href$="&tab=${tab}"]`).addClass('active');
            }
        }

        // Event delegation for content clicks
        $(document).on('click', '.accordion-button', function () {
            const moduleIndex = $(this).closest('.accordion-item').index();
            const tab = 'learning-materials';
            updateSidebarActiveState(tab, moduleIndex);

            // Trigger dynamic content loading (optional)
            loadContent(tab, moduleIndex);
        });

        // On sidebar link click
        $('.menu-item').on('click', function (event) {
            event.preventDefault();
            const url = new URL($(this).attr('href'), window.location.origin);
            const tab = url.searchParams.get('tab');
            const module = url.searchParams.get('module');
            updateSidebarActiveState(tab, module);

            // Load the content dynamically
            loadContent(tab, module);
        });

        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'pre-test';
            const module = urlParams.get('module');

            // Function to load dynamic content while preserving accordion state
            function loadContent(tab, module = null) {
                $.ajax({
                    url: "CourseContent.php",
                    type: "GET",
                    data: {
                        course_id: "<?php echo $course_id; ?>",
                        tab: tab,
                        module: module
                    },
                    success: function (response) {
                        const newContent = $(response).find('#content-section').html();
                        $('#content-section').html(newContent);

                        // Update the active state for the accordion
                        updateSidebarActiveState(tab, module);

                        // Reinitialize Bootstrap accordion
                        initializeAccordion();
                    },
                    error: function () {
                        alert("Failed to load content.");
                    }
                });
            }

            // Function to highlight the active sidebar item
            function updateSidebarActiveState(tab, module = null) {
                $('.menu-item').removeClass('active');
                $('.submenu .menu-item').removeClass('active');

                if (module !== null) {
                    $(`a[href$="&tab=${tab}&module=${module}"]`).addClass('active');
                } else {
                    $(`a[href$="&tab=${tab}"]`).addClass('active');
                }
            }

            // Reinitialize Bootstrap accordion (required after content is dynamically replaced)
            function initializeAccordion() {
                $('.accordion-button').off('click').on('click', function (event) {
                    event.stopPropagation(); // Prevent interference with Bootstrap default behavior
                    const moduleIndex = $(this).closest('.accordion-item').index();
                    const tab = 'learning-materials';

                    // Update the sidebar active state
                    updateSidebarActiveState(tab, moduleIndex);
                });
            }

            // Load initial content
            loadContent(tab, module);

            // Update content dynamically when a sidebar link is clicked
            $('.menu-item').on('click', function (event) {
                event.preventDefault();
                const url = new URL($(this).attr('href'), window.location.origin);
                const tab = url.searchParams.get('tab');
                const module = url.searchParams.get('module');

                updateSidebarActiveState(tab, module);
                loadContent(tab, module);
            });

            // Initialize accordion interactions
            initializeAccordion();
        });


    </script>
</body>

</html>