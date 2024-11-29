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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    try {
        // Add Material
        if ($_POST['action'] === 'add') {
            $module_title = $_POST['module_title'];
            $module_description = $_POST['module_description'];

            // Validate inputs
            if (empty($module_title) || empty($module_description)) {
                echo json_encode(["status" => "error", "message" => "Module title and description are required."]);
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

            $stmt = $conn->prepare("INSERT INTO learning_materials (course_id, module_title, module_description, video_url, pdf_url) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Database prepare failed: " . $conn->error);
            }
            $stmt->bind_param("issss", $course_id, $module_title, $module_description, $video_path, $pdf_path);

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
</head>

<body>
    <div class="container mt-5">
        <h1>Manage Learning Materials</h1>
        <h4 class="text-secondary">Course ID: <span id="courseIdDisplay"></span></h4>

        <!-- Form to Add Material -->
        <form id="addMaterialForm" enctype="multipart/form-data">
            <input type="hidden" id="courseId" value="">
            <div class="mb-3">
                <label for="moduleTitle" class="form-label">Module Title</label>
                <input type="text" id="moduleTitle" name="module_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="moduleDescription" class="form-label">Module Description</label>
                <textarea id="moduleDescription" name="module_description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="videoFile" class="form-label">Video File</label>
                <input type="file" id="videoFile" name="video_file" class="form-control" accept="video/*">
            </div>
            <div class="mb-3">
                <label for="pdfFile" class="form-label">PDF File</label>
                <input type="file" id="pdfFile" name="pdf_file" class="form-control" accept="application/pdf">
            </div>
            <button type="submit" class="btn btn-primary">Add Material</button>
        </form>

        <!-- List of Materials -->
        <div id="materialList" class="mt-4">
            <h2>Existing Materials</h2>
            <ul id="materials"></ul>
        </div>
    </div>

    <script>
        const courseId = new URLSearchParams(window.location.search).get("course_id");
        document.getElementById("courseId").value = courseId;
        document.getElementById("courseIdDisplay").textContent = courseId;

        // Add Material
        document.getElementById("addMaterialForm").addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            formData.append("action", "add");

            try {
                const response = await fetch("learning_materials.php?course_id=" + courseId, {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();
                alert(result.message);
                fetchMaterials(); // Refresh the material list
            } catch (error) {
                console.error("Error adding material:", error);
                alert("An error occurred. Please try again.");
            }
        });

        // Fetch Materials
        async function fetchMaterials() {
            const formData = new FormData();
            formData.append("action", "fetch");

            const response = await fetch("learning_materials.php?course_id=" + courseId, {
                method: "POST",
                body: formData,
            });

            const materials = await response.json();
            const materialList = document.getElementById("materials");
            materialList.innerHTML = ""; // Clear the list before adding new items
            materials.forEach((material) => {
                materialList.innerHTML += `
                    <li>
                        <strong>${material.module_title}</strong> - ${material.module_description}
                        <br>
                        <a href="${material.video_url}" target="_blank">Video</a> | 
                        <a href="${material.pdf_url}" target="_blank">PDF</a>
                        <button onclick="deleteMaterial(${material.LM_id})" class="btn btn-danger btn-sm">Delete</button>
                    </li>
                `;
            });
        }

        // Delete Material
        async function deleteMaterial(LM_id) {
            const formData = new FormData();
            formData.append("action", "delete");
            formData.append("LM_id", LM_id);

            const response = await fetch("learning_materials.php?course_id=" + courseId, {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            alert(result.message);
            fetchMaterials(); // Refresh the material list
        }

        // Fetch materials on page load
        fetchMaterials();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>