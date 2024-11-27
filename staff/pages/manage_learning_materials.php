<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

$course_id = $_GET['course_id'];

// Fetch course details
$course_query = $conn->query("SELECT course_name FROM courses WHERE course_id = $course_id");
$course = $course_query->fetch_assoc();

// Fetch existing modules
$modules_query = $conn->query("SELECT * FROM learning_modules WHERE course_id = $course_id");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Learning Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container py-5">
        <h1 class="text-primary text-center mb-4">Manage Learning Materials for
            <?php echo htmlspecialchars($course['course_name']); ?></h1>

        <div class="text-end mb-3">
            <button class="btn btn-success" id="add-module-btn">Add Module</button>
        </div>

        <form id="modules-form" method="POST" action="save_learning_materials.php">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <div id="modules-container">
                <?php while ($module = $modules_query->fetch_assoc()) { ?>
                    <div class="card mb-4 module-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Module: <?php echo htmlspecialchars($module['module_name']); ?></span>
                            <button type="button" class="btn btn-danger btn-sm remove-module-btn">Remove Module</button>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="modules[<?php echo $module['module_id']; ?>][module_id]"
                                value="<?php echo $module['module_id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Module Name</label>
                                <input type="text" name="modules[<?php echo $module['module_id']; ?>][module_name]"
                                    class="form-control" value="<?php echo htmlspecialchars($module['module_name']); ?>"
                                    required>
                            </div>
                            <div class="materials-container">
                                <!-- Fetch materials for this module -->
                                <?php
                                $materials_query = $conn->query("SELECT * FROM learning_materials WHERE module_id = " . $module['module_id']);
                                while ($material = $materials_query->fetch_assoc()) { ?>
                                    <div class="material-item mb-3">
                                        <select class="form-select mb-2"
                                            name="modules[<?php echo $module['module_id']; ?>][materials][][type]" required>
                                            <option value="text" <?php echo $material['type'] === 'text' ? 'selected' : ''; ?>>
                                                Text</option>
                                            <option value="video" <?php echo $material['type'] === 'video' ? 'selected' : ''; ?>>
                                                Video</option>
                                            <option value="file" <?php echo $material['type'] === 'file' ? 'selected' : ''; ?>>
                                                File</option>
                                        </select>
                                        <textarea class="form-control"
                                            name="modules[<?php echo $module['module_id']; ?>][materials][][content]"
                                            placeholder="Enter content"
                                            required><?php echo htmlspecialchars($material['content']); ?></textarea>
                                        <button type="button"
                                            class="btn btn-danger btn-sm mt-2 remove-material-btn">Remove</button>
                                    </div>
                                <?php } ?>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm add-material-btn mt-3">Add Material</button>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <script>
        // Add new module
        document.getElementById('add-module-btn').addEventListener('click', function () {
            const moduleId = Date.now();
            const moduleTemplate = `
                <div class="card mb-4 module-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>New Module</span>
                        <button type="button" class="btn btn-danger btn-sm remove-module-btn">Remove Module</button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Module Name</label>
                            <input type="text" name="modules[new_${moduleId}][module_name]" class="form-control" required>
                        </div>
                        <div class="materials-container"></div>
                        <button type="button" class="btn btn-primary btn-sm add-material-btn mt-3">Add Material</button>
                    </div>
                </div>`;
            document.getElementById('modules-container').insertAdjacentHTML('beforeend', moduleTemplate);
        });

        // Add new material
        $(document).on('click', '.add-material-btn', function () {
            const materialTemplate = `
                <div class="material-item mb-3">
                    <select class="form-select mb-2" name="materials[new][][type]" required>
                        <option value="text">Text</option>
                        <option value="video">Video</option>
                        <option value="file">File</option>
                    </select>
                    <textarea class="form-control" name="materials[new][][content]" placeholder="Enter content" required></textarea>
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-material-btn">Remove</button>
                </div>`;
            $(this).siblings('.materials-container').append(materialTemplate);
        });

        // Remove module or material
        $(document).on('click', '.remove-module-btn', function () {
            $(this).closest('.module-card').remove();
        });
        $(document).on('click', '.remove-material-btn', function () {
            $(this).closest('.material-item').remove();
        });
    </script>
</body>

</html>