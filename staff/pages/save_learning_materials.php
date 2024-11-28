<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

$course_id = $_POST['course_id'] ?? null;

// Check if course_id is set
if ($course_id === null) {
    echo "Invalid course ID.";
    exit;
}

// Function to handle file uploads
function handleFileUpload($file, $target_dir, $file_type)
{
    $allowed_extensions = [
        'video' => ['mp4', 'avi', 'mov'],
        'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx']
    ];

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid($file_type . '_', true) . '.' . $file_extension;
    $target_file = $target_dir . $file_name;

    // Validate file extension
    if (!in_array($file_extension, $allowed_extensions[$file_type])) {
        echo "Invalid file type.";
        return false;
    }

    // Check if directory exists or create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    } else {
        echo "Error uploading file.";
        return false;
    }
}

// Loop through each module and save the contents
foreach ($_POST['modules'] as $module_id => $module_data) {
    $module_name = $module_data['module_name'];
    // Insert module into database
    $stmt = $conn->prepare("INSERT INTO modules (course_id, module_name) VALUES (?, ?)");
    $stmt->bind_param("is", $course_id, $module_name);
    $stmt->execute();
    $module_id_db = $stmt->insert_id; // Get the inserted module ID

    // Handle the contents (files, text, video) for each module
    if (isset($module_data['contents'])) {
        foreach ($module_data['contents'] as $content_id => $content) {
            // Handle text content
            if (isset($content['text']) && !empty($content['text'])) {
                $content_type = 'text'; // Use a variable
                $stmt = $conn->prepare("INSERT INTO module_contents (module_id, content_type, content_text) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $module_id_db, $content_type, $content['text']);
                $stmt->execute();
            }
            // Handle video content
            elseif (isset($content['video']) && !empty($content['video'])) {
                $content_type = 'video'; // Use a variable
                $video_file = $_FILES['modules'][$module_id]['contents'][$content_id]['video'];
                $video_path = handleFileUpload($video_file, "../../staff/upload/videos/", 'video');
                if ($video_path) {
                    $stmt = $conn->prepare("INSERT INTO module_contents (module_id, content_type, content_video_path) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $module_id_db, $content_type, $video_path);
                    $stmt->execute();
                }
            }
            // Handle file content
            elseif (isset($content['file']) && !empty($content['file'])) {
                $content_type = 'file'; // Use a variable
                $file = $_FILES['modules'][$module_id]['contents'][$content_id]['file'];
                $file_path = handleFileUpload($file, "../../staff/upload/files/", 'document');
                if ($file_path) {
                    $stmt = $conn->prepare("INSERT INTO module_contents (module_id, content_type, content_file_path) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $module_id_db, $content_type, $file_path);
                    $stmt->execute();
                }
            }
        }
    }
}

header("Location: manage_learning_materials.php?course_id=$course_id");
exit;

?>