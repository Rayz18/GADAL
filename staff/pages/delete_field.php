<?php
include '../../config/config.php';
header('Content-Type: application/json');
session_start();

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['field_id']) || empty($data['field_id'])) {
        throw new Exception("Field ID is required.");
    }

    $field_id = $data['field_id'];

    $conn->autocommit(false); // Begin transaction

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM seminar_fields WHERE field_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("i", $field_id);

    if ($stmt->execute()) {
        $conn->commit(); // Commit changes
        echo json_encode(['success' => true, 'message' => 'Field deleted successfully.']);
    } else {
        $conn->rollback(); // Rollback on failure
        throw new Exception("Error executing delete query: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
