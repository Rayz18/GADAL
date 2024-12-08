<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo "Unauthorized access.";
    exit;
}

$action = $_POST['action'] ?? null;
$content_type = $_POST['content_type'] ?? null;
$content_id = $_POST['content_id'] ?? null;

if (!$action || !$content_type || !$content_id) {
    echo "Invalid input.";
    exit;
}

$content_mappings = [
    "Programs" => ['table' => 'programs', 'id_column' => 'program_id'],
    "Courses" => ['table' => 'courses', 'id_column' => 'course_id'],
    "Learning Materials" => ['table' => 'learning_materials', 'id_column' => 'LM_id'],
];

if (!array_key_exists($content_type, $content_mappings)) {
    echo "Invalid content type.";
    exit;
}

$table = $content_mappings[$content_type]['table'];
$id_column = $content_mappings[$content_type]['id_column'];

$status = ($action === 'approve') ? 'approved' : 'declined';
$query = "UPDATE $table SET status = ? WHERE $id_column = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $status, $content_id);

if ($stmt->execute()) {
    echo "Content $status successfully.";
} else {
    echo "Failed to update status: " . $conn->error;
}

$stmt->close();
$conn->close();
?>

