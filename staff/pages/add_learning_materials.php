<?php
include '../../config/config.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$course_id = $_GET['course_id'] ?? null;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['learning_file'])) {
    $file = $_FILES['learning_file'];
    $target_dir = "../../staff/upload/add_learning_materials/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($file["name"]);

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $query = "INSERT INTO learning_materials (course_id, file_path) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $course_id, $target_file);

        if ($stmt->execute()) {
            $success_message = "Learning material uploaded successfully!";
        } else {
            $error_message = "Failed to upload learning material.";
        }
    } else {
        $error_message = "Failed to upload file.";
    }
}

// Fetch existing learning materials
$query = "SELECT * FROM learning_materials WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/add_learning_materials.css">
    <link rel="stylesheet" href="../../includes/assets/StaffNavBar.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../includes/assets/sidebarToggle.js" defer></script>
</head>

<body>
    <?php include '../../includes/StaffNavBar.php'; ?>

    <!-- Sidebar -->
    <div class="sidebar collapsed"></div>

    <!-- Sidebar Toggle Button -->
    <div id="toggle-sidebar" class="toggle-sidebar"></div>

    <!-- Main Content -->
    <div id="content" class="container-fluid">
        <h1 class="page-title">LEARNING MATERIALS</h1>

        <!-- Success/Error Messages -->
        <div class="messages">
            <?php if ($success_message): ?>
                <div id="successMessage" class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div id="errorMessage" class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-5 align-items-stretch">
            <!-- Upload Learning Material Form -->
            <div class="col-lg-6">
                <div class="card upload-card">
                    <div class="card-header upload-header">Upload Learning Material</div>
                    <div class="card-body upload-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="learning_file" class="form-label fw-bold">File:</label>
                                <input type="file" name="learning_file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Upload Material</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Learning Materials -->
            <div class="col-lg-6">
                <div class="card existing-card">
                    <div class="card-header existing-header">Existing Learning Materials</div>
                    <div class="card-body existing-body">
                        <table class="table table-striped table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th class="file-name-column">File Name</th>
                                    <th class="action-column">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="file-name">
                                            <span><?php echo basename($row['file_path']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo $row['file_path']; ?>" target="_blank"
                                                class="btn btn-sm btn-success">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 3000);
    </script>
</body>

</html>