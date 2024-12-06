<?php
// Include database configuration
include '../../config/config.php';

// Enable debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if course_id is provided
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("<h1>Error: Course ID is required in the URL.</h1>");
}
$course_id = (int) $_GET['course_id'];

// Fetch the course name from the database
$course_query = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
if ($course_result->num_rows === 0) {
    die("<h1>Error: Invalid Course ID.</h1>");
}
$course_name = $course_result->fetch_assoc()['course_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    try {
        // Add Material
        if ($_POST['action'] === 'add') {
            $module_title = $_POST['module_title'];
            $module_discussion = $_POST['module_discussion'];
            $video_title = $_POST['video_title'] ?? null;
            $pdf_title = $_POST['pdf_title'] ?? null;

            // Validate inputs
            if (empty($module_title) || empty($module_discussion)) {
                echo json_encode(["status" => "error", "message" => "Module title and discussion are required."]);
                exit;
            }

            // Handle file uploads
            $upload_dir = '../../staff/upload/materials/';
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception("Failed to create upload directory.");
                }
            }
            $video_path = null;
            $pdf_path = null;

            if (!empty($_FILES['video_file']['name'])) {
                $video_file = $_FILES['video_file'];
                $video_path = $upload_dir . uniqid() . '_' . basename($video_file['name']);
                if (!move_uploaded_file($video_file['tmp_name'], $video_path)) {
                    throw new Exception("Failed to upload video.");
                }
            }

            if (!empty($_FILES['pdf_file']['name'])) {
                $pdf_file = $_FILES['pdf_file'];
                $pdf_path = $upload_dir . uniqid() . '_' . basename($pdf_file['name']);
                if (!move_uploaded_file($pdf_file['tmp_name'], $pdf_path)) {
                    throw new Exception("Failed to upload PDF.");
                }
            }

            $stmt = $conn->prepare(
                "INSERT INTO learning_materials (course_id, module_title, module_discussion, video_url, video_title, pdf_url, pdf_title) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                throw new Exception("Database prepare failed: " . $conn->error);
            }
            $stmt->bind_param("issssss", $course_id, $module_title, $module_discussion, $video_path, $video_title, $pdf_path, $pdf_title);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Material added successfully."]);
            } else {
                throw new Exception("Failed to save material in database: " . $stmt->error);
            }
            exit;
        }

        // Fetch Materials
        if ($_POST['action'] === 'fetch') {
            $stmt = $conn->prepare("SELECT * FROM learning_materials WHERE course_id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $materials = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($materials);
            exit;
        }

        // Delete Material
        if ($_POST['action'] === 'delete') {
            $LM_id = $_POST['LM_id'];

            // Fetch file paths to delete
            $stmt = $conn->prepare("SELECT video_url, pdf_url FROM learning_materials WHERE LM_id = ?");
            $stmt->bind_param("i", $LM_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $material = $result->fetch_assoc();

            // Delete files from the server
            if ($material) {
                if (!empty($material['video_url']) && file_exists($material['video_url'])) {
                    unlink($material['video_url']);
                }
                if (!empty($material['pdf_url']) && file_exists($material['pdf_url'])) {
                    unlink($material['pdf_url']);
                }
            }

            // Delete material from the database
            $stmt = $conn->prepare("DELETE FROM learning_materials WHERE LM_id = ?");
            $stmt->bind_param("i", $LM_id);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Material deleted successfully."]);
            } else {
                throw new Exception("Failed to delete material from database.");
            }
            exit;
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Learning Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/learning_materials.css">
</head>

<body>
    <div class="layout">
        <!-- Sidebar -->
        <div id="toggle-sidebar" class="toggle-sidebar">
            <!-- Sidebar content can go here -->
        </div>

        <?php include '../../public/includes/StaffNavBar.php'; ?>

        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Toggle Sidebar Icon -->
            <div id="toggle-sidebar" class="toggle-sidebar"></div>
            <div class="header">
                <h1>Manage Learning Materials</h1>
                <h4 class="text-light">Course: <span
                        id="courseNameDisplay"><?php echo htmlspecialchars($course_name); ?></span></h4>
            </div>

            <!-- Add Material Form -->
            <div class="form-container">
                <h4 class="section-title">Add New Learning Material</h4>
                <form id="addMaterialForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="moduleTitle" class="form-label">Module Title</label>
                        <input type="text" id="moduleTitle" name="module_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="moduleDiscussion" class="form-label">Module Discussion</label>
                        <textarea id="moduleDiscussion" name="module_discussion" class="form-control"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="videoFile" class="form-label">Upload Video</label>
                        <input type="file" id="videoFile" name="video_file" class="form-control" accept="video/*">
                    </div>
                    <div class="mb-3">
                        <label for="videoTitle" class="form-label">Video Title</label>
                        <input type="text" id="videoTitle" name="video_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label">Upload PDF</label>
                        <input type="file" id="pdfFile" name="pdf_file" class="form-control" accept="application/pdf">
                    </div>
                    <div class="mb-3">
                        <label for="pdfTitle" class="form-label">PDF Title</label>
                        <input type="text" id="pdfTitle" name="pdf_title" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Add Material</button>
                </form>
            </div>

            <!-- Existing Materials Section -->
            <div class="materials-container">
                <h4 class="section-title">Existing Learning Materials</h4>
                <div id="materialList">
                    <div class="accordion" id="materialsAccordion"></div>
                </div>
            </div>
        </div>

        <script>
            const courseId = new URLSearchParams(window.location.search).get("course_id");
            document.getElementById("addMaterialForm").addEventListener("submit", async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append("action", "add");

                const response = await fetch("learning_materials.php?course_id=" + courseId, { method: "POST", body: formData });
                const result = await response.json();
                alert(result.message);
                fetchMaterials();
            });

            async function fetchMaterials() {
                const formData = new FormData();
                formData.append("action", "fetch");
                const response = await fetch("learning_materials.php?course_id=" + courseId, { method: "POST", body: formData });
                const materials = await response.json();
                const materialList = document.getElementById("materialsAccordion");
                materialList.innerHTML = "";

                materials.forEach((material, index) => {
                    let videoHTML = "";
                    let pdfHTML = "";

                    // Add video HTML only if video_url is present
                    if (material.video_url) {
                        videoHTML = `
                            <div class="video-container mt-3">
                                <h5>${material.video_title || "Video"}</h5>
                                <video controls>
                                    <source src="${material.video_url}" type="video/mp4">
                                    Your browser does not support video playback.
                                </video>
                            </div>
                        `;
                    }

                    // Add PDF HTML only if pdf_url is present
                    if (material.pdf_url) {
                        pdfHTML = `
                            <div class="file-container mt-3">
                                <h5>${material.pdf_title || "PDF File"}</h5>
                                <a href="${material.pdf_url}" target="_blank" class="btn btn-secondary">View PDF</a>
                            </div>
                        `;
                    }

                    // Add discussion with preserved formatting
                    materialList.innerHTML += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                                    ${material.module_title}
                                </button>
                            </h2>
                            <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#materialsAccordion">
                                <div class="accordion-body">
                                    <p><strong>Discussion:</strong></p>
                                    <div class="preserved-text">
                                        ${material.module_discussion}
                                    </div>
                                    ${videoHTML}
                                    ${pdfHTML}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            fetchMaterials();
        </script>

        <script>document.addEventListener("DOMContentLoaded", function () {
                const sidebar = document.getElementById("sidebar");
                const content = document.getElementById("content");
                const toggleButton = document.getElementById("toggle-sidebar");

                toggleButton.addEventListener("click", function () {
                    if (sidebar.classList.contains("open")) {
                        // Close the sidebar
                        sidebar.classList.remove("open");
                        content.classList.remove("shifted");
                    } else {
                        // Open the sidebar
                        sidebar.classList.add("open");
                        content.classList.add("shifted");
                    }
                });
            });</script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>