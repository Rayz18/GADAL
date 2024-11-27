<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch and validate the inputs
$action = $_POST['action'] ?? null;
$content_type = $_POST['content_type'] ?? null;
$content_mdrtn_id = $_POST['content_mdrtn_id'] ?? null;

if (!$action || !$content_type || !$content_mdrtn_id) {
    die("Invalid request. Missing required parameters.");
}

// Determine the status based on the action
$status = ($action === 'approve') ? 'approved' : 'declined';

// Define mappings for content types
$content_type_mapping = [
    'programs' => 'program',
    'courses' => 'course',
    'sections' => 'section',
    'learning materials' => 'material',
    'course videos' => 'course_video',
    'post-test questions' => 'post_test',
    'pre-test questions' => 'pre_test'
];

$content_mappings = [
    'program' => ['table' => 'programs', 'id_field' => 'program_id'],
    'course' => ['table' => 'courses', 'id_field' => 'course_id'],
    'section' => ['table' => 'course_sections', 'id_field' => 'section_id'],
    'material' => ['table' => 'learning_materials', 'id_field' => 'LM_id'],
    'course_video' => ['table' => 'course_videos', 'id_field' => 'course_videos_id'],
    'post_test' => ['table' => 'post_test_questions', 'id_field' => 'post_test_id'],
    'pre_test' => ['table' => 'pre_test_questions', 'id_field' => 'pre_test_id'],
];

// Map content type to table and ID field
$content_type = $content_type_mapping[strtolower($content_type)] ?? strtolower($content_type);

if (!array_key_exists($content_type, $content_mappings)) {
    die("Invalid content type specified.");
}

$table = $content_mappings[$content_type]['table'];
$id_field = $content_mappings[$content_type]['id_field'];

// Prepare the update query
$stmt = $conn->prepare("UPDATE $table SET status = ? WHERE $id_field = ?");
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param("si", $status, $content_mdrtn_id);
if (!$stmt->execute()) {
    die("Failed to execute query: " . $stmt->error);
}

// Close the statement before redirecting
$stmt->close();

// Redirect after successfully updating the status
header("Location: content_moderation.php?status=updated");
exit;
?>