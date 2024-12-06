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
    error_log("Missing parameters: action=$action, content_type=$content_type, content_mdrtn_id=$content_mdrtn_id");
    die("Invalid request. Missing required parameters.");
}

$status = ($action === 'approve') ? 'approved' : 'declined';

$content_type_mapping = [
    'programs' => 'program',
    'courses' => 'course',
    'learning materials' => 'material',
    'post-test questions' => 'post_test',
    'pre-test questions' => 'pre_test',
    'quiz questions' => 'quiz'
];

$content_mappings = [
    'program' => ['table' => 'programs', 'id_field' => 'program_id'],
    'course' => ['table' => 'courses', 'id_field' => 'course_id'],
    'material' => ['table' => 'learning_materials', 'id_field' => 'LM_id'],
    'post_test' => ['table' => 'post_test_questions', 'id_field' => 'post_test_id'],
    'pre_test' => ['table' => 'pre_test_questions', 'id_field' => 'pre_test_id'],
    'quiz' => ['table' => 'quiz_questions', 'id_field' => 'quiz_id']
];

$content_type = $content_type_mapping[strtolower($content_type)] ?? strtolower($content_type);

if (!array_key_exists($content_type, $content_mappings)) {
    die("Invalid content type specified.");
}

$table = $content_mappings[$content_type]['table'];
$id_field = $content_mappings[$content_type]['id_field'];

$sql = "UPDATE $table SET status = ? WHERE $id_field = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Failed to prepare statement: " . $conn->error);
    die("Failed to prepare statement.");
}

$stmt->bind_param("si", $status, $content_mdrtn_id);

if (!$stmt->execute()) {
    error_log("Query execution failed: " . $stmt->error);
    die("Failed to execute query.");
}

if ($stmt->affected_rows > 0) {
    header("Location: content_moderation.php?status=success&type=$content_type");
} else {
    header("Location: content_moderation.php?status=failure&type=$content_type");
}

error_log("Action: $action, Content Type: $content_type, Content ID: $content_mdrtn_id");

$stmt->close();
exit;
