<?php
include '../../config/config.php';
session_start();

$seminar_id = $_POST['seminar_id'] ?? null;
$attendance_instructions = $_POST['attendance_instructions'] ?? '';

if (!$seminar_id) {
    die("Invalid seminar ID.");
}

try {
    // Save instructions to the seminars table
    $stmt = $conn->prepare("UPDATE seminars SET attendance_instructions = ? WHERE seminar_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $attendance_instructions, $seminar_id);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception("Failed to prepare statement for updating instructions in seminars table.");
    }

    // Loop through each dynamic field and insert/update in attendance_fields table
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'field_') === 0) {
            $field_id = str_replace('field_', '', $key);

            if (is_array($value)) { // Handle options for checkbox or multi-select fields
                $value = json_encode($value);
            }

            // Update field value for each field
            $stmt = $conn->prepare("UPDATE attendance_fields SET field_value = ? WHERE field_id = ? AND seminar_id = ?");
            if ($stmt) {
                $stmt->bind_param("sii", $value, $field_id, $seminar_id);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Failed to prepare statement for updating field $field_id.");
            }
        }
    }

    // Redirect back with success message
    header("Location: add_attendance.php?seminar_id=$seminar_id&success=1");
    exit();
} catch (Exception $e) {
    // Log error or display a friendly message
    error_log($e->getMessage());
    die("An error occurred. Please try again.");
}
