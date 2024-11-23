<?php
include '../../config/config.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
$seminar_id = $data['seminar_id'] ?? null;
$field_label = $data['field_label'] ?? '';
$field_type = $data['field_type'] ?? '';
$required = isset($data['required']) && $data['required'] ? 1 : 0;
$options = $data['options'] ?? [];

if (!$seminar_id || !$field_label || !$field_type) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

// Prepare the SQL statement to insert the field
$stmt = $conn->prepare("INSERT INTO attendance_fields (seminar_id, field_label, field_type, required, options) VALUES (?, ?, ?, ?, ?)");

// Convert options array to JSON if field type requires options
$options_json = in_array($field_type, ['radio', 'dropdown']) ? json_encode($options) : null;

$stmt->bind_param("issis", $seminar_id, $field_label, $field_type, $required, $options_json);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insertion failed.']);
}

$stmt->close();
$conn->close();
