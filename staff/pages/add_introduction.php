<?php
include '../../config/config.php';
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$course_id = $_GET['course_id'] ?? null;
$referrer = $_GET['ref'] ?? 'manage_programs.php'; // Default to manage_programs.php if ref is not set
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $section_content = $_POST['section_content'];

    // Check if introduction already exists
    $query = "SELECT course_id FROM course_sections WHERE course_id = ? AND section_name = 'Introduction'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update introduction
        $update_query = "UPDATE course_sections SET section_content = ? WHERE course_id = ? AND section_name = 'Introduction'";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $section_content, $course_id);
    } else {
        // Insert new introduction
        $insert_query = "INSERT INTO course_sections (course_id, section_name, section_content) VALUES (?, 'Introduction', ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("is", $course_id, $section_content);
    }

    if ($stmt->execute()) {
        $success_message = "Introduction updated successfully!";
    } else {
        $error_message = "Failed to update the introduction.";
    }
} elseif (isset($_POST['back'])) {
    // Redirect to the referrer page
    header("Location: $referrer");
    exit();
}

// Fetch the current introduction content
$intro_query = "SELECT section_content FROM course_sections WHERE course_id = ? AND section_name = 'Introduction'";
$stmt = $conn->prepare($intro_query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$current_intro = $result->fetch_assoc()['section_content'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course Introduction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../includes/assets/StaffNavBar.css">
    <link rel="stylesheet" href="../../staff/assets/css/add_course_section.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../includes/assets/sidebarToggle.js" defer></script>
</head>

<body>
    <?php include '../../includes/StaffNavBar.php'; ?>

    <div class="layout">
        <!-- Sidebar -->
        <div id="toggle-sidebar" class="toggle-sidebar">
            <!-- Sidebar content can go here -->
        </div>

        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Toggle Sidebar Icon -->
            <div id="toggle-sidebar" class="toggle-sidebar"></div>
            <h1 class="page-title">EDIT COURSE INTRODUCTION</h1>

            <!-- Success/Error Messages -->
            <div class="messages">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>

            <form method="POST" class="form-container">
                <div class="mb-3">
                    <textarea name="section_content" id="section_content" class="form-control" rows="12"
                        style="width: 100%; max-width: 1200px;"><?php echo htmlspecialchars($current_intro); ?></textarea>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="save" class="btn btn-primary">Save Introduction</button>
                    <button type="submit" name="back" class="btn btn-secondary">Back</button>
                    <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($referrer); ?>">
                </div>
            </form>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const successMessage = document.querySelector(".alert-success");
                    const errorMessage = document.querySelector(".alert-danger");

                    setTimeout(() => {
                        if (successMessage) successMessage.style.display = "none";
                        if (errorMessage) errorMessage.style.display = "none";
                    }, 3000);
                });

                document.addEventListener("DOMContentLoaded", function () {
                    const sidebar = document.getElementById("sidebar");
                    const content = document.getElementById("content");
                    const toggleButton = document.getElementById("toggle-sidebar");

                    toggleButton.addEventListener("click", function () {
                        if (sidebar.classList.contains("open")) {
                            // Close the sidebar
                            sidebar.classList.remove("open");
                            content.classList.remove("shifted");
                        } else {
                            // Open the sidebar
                            sidebar.classList.add("open");
                            content.classList.add("shifted");
                        }
                    });
                });
            </script>
</body>

</html>