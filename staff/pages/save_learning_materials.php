<?php
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $modules = $_POST['modules'];

    // Save modules and materials
    foreach ($modules as $module) {
        if (isset($module['module_id'])) {
            // Update existing module
            $module_id = $module['module_id'];
            $module_name = $module['module_name'];
            $conn->query("UPDATE learning_modules SET module_name = '$module_name' WHERE module_id = $module_id");
        } else {
            // Insert new module
            $module_name = $module['module_name'];
            $conn->query("INSERT INTO learning_modules (course_id, module_name) VALUES ($course_id, '$module_name')");
            $module_id = $conn->insert_id;
        }

        // Save materials
        foreach ($module['materials'] as $material) {
            $type = $material['type'];
            $content = $material['content'];
            $conn->query("INSERT INTO learning_materials (module_id, type, content) VALUES ($module_id, '$type', '$content')");
        }
    }
    header("Location: manage_learning_materials.php?course_id=$course_id");
}
?>