<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}

$course_id = $_GET['course_id'];

// Fetch course details
$course_query = $conn->query("SELECT course_name FROM courses WHERE course_id = $course_id");
$course = $course_query->fetch_assoc();
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
            <?php echo htmlspecialchars($course['course_name']); ?>
        </h1>

        <div class="text-end mb-3">
            <button class="btn btn-success" id="add-module-btn">Add Module</button>
        </div>

        <form id="modules-form" method="POST" action="save_learning_materials.php" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <div id="modules-container">
                <!-- Modules will be dynamically added here -->
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
                <div class="card mb-3 module-card" id="module-card-${moduleId}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <input type="text" class="form-control w-75 module-name" 
                               name="modules[new_${moduleId}][module_name]" 
                               placeholder="Enter Module Name" required>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#module-${moduleId}" aria-expanded="false">
                                Module Contents
                            </button>
                            <button type="button" class="btn btn-danger remove-module-btn" data-module-id="${moduleId}">
                                Delete Module
                            </button>
                        </div>
                    </div>
                    <div id="module-${moduleId}" class="collapse">
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-success add-content-btn" data-type="text"
                                    data-module-id="${moduleId}">Add Text</button>
                                <button type="button" class="btn btn-sm btn-info add-content-btn" data-type="video"
                                    data-module-id="${moduleId}">Add Video</button>
                                <button type="button" class="btn btn-sm btn-warning add-content-btn" data-type="file"
                                    data-module-id="${moduleId}">Add File</button>
                            </div>
                            <ul class="list-group content-list" data-module-id="${moduleId}">
                                <!-- Module contents will be dynamically added here -->
                            </ul>
                        </div>
                    </div>
                </div>`;
            document.getElementById('modules-container').insertAdjacentHTML('beforeend', moduleTemplate);
        });

        // Add content (text, video, file) to a module
        $(document).on('click', '.add-content-btn', function () {
            const moduleId = $(this).data('module-id');
            const contentType = $(this).data('type'); // "text", "video", or "file"
            const contentList = $(`ul[data-module-id="${moduleId}"]`);
            const contentId = Date.now();

            let contentTemplate = '';

            if (contentType === 'text') {
                contentTemplate = `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Text</span>
                            <button type="button" class="btn btn-danger btn-sm remove-content-btn">Remove</button>
                        </div>
                        <input type="text" class="form-control mt-2"
                               name="modules[new_${moduleId}][contents][${contentId}][text]"
                               placeholder="Enter Text Content" required>
                    </li>`;
            } else if (contentType === 'video') {
                contentTemplate = `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Video</span>
                            <button type="button" class="btn btn-danger btn-sm remove-content-btn">Remove</button>
                        </div>
                        <input type="file" class="form-control mt-2"
                               name="modules[new_${moduleId}][contents][${contentId}][video]" 
                               accept="video/*" required>
                    </li>`;
            } else if (contentType === 'file') {
                contentTemplate = `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>File</span>
                            <button type="button" class="btn btn-danger btn-sm remove-content-btn">Remove</button>
                        </div>
                        <input type="file" class="form-control mt-2"
                               name="modules[new_${moduleId}][contents][${contentId}][file]" 
                               required>
                    </li>`;
            }

            contentList.append(contentTemplate);
        });

        // Remove content
        $(document).on('click', '.remove-content-btn', function () {
            $(this).closest('li').remove();
        });

        // Remove module
        $(document).on('click', '.remove-module-btn', function () {
            const moduleId = $(this).data('module-id');
            $(`#module-card-${moduleId}`).remove();
        });
    </script>
</body>

</html>