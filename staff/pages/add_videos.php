<?php
include '../../config/config.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit();
}

$course_id = $_GET['course_id'] ?? null;
$video_success_message = '';
$video_error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['video_file'])) {
    $file = $_FILES['video_file'];
    $target_dir = "../../staff/upload/videos/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($file["name"]);

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $query = "INSERT INTO course_videos (course_id, video_path) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $course_id, $target_file);

        if ($stmt->execute()) {
            $video_success_message = "Video uploaded successfully!";
        } else {
            $video_error_message = "Failed to upload video.";
        }
    } else {
        $video_error_message = "Failed to upload file.";
    }
}

// Fetch existing videos
$query = "SELECT * FROM course_videos WHERE course_id = ?";
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
    <title>Upload Videos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/add_videos.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <!-- Sidebar -->
    <div class="sidebar collapsed">
        <!-- Sidebar content -->
    </div>

    <!-- Sidebar Toggle Button -->
    <div id="toggle-sidebar" class="toggle-sidebar"></div>

    <!-- Main Content -->
    <div class="container-fluid main-container">
        <h1 class="page-title">UPLOAD COURSE VIDEOS</h1>

        <!-- Success/Error Messages -->
        <div class="messages">
            <?php if ($video_success_message): ?>
                <div id="successMessage" class="alert alert-success">
                    <?php echo $video_success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($video_error_message): ?>
                <div id="errorMessage" class="alert alert-danger">
                    <?php echo $video_error_message; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-5 align-items-stretch">
            <!-- Upload Video Form -->
            <div class="col-lg-6">
                <div class="card upload-card">
                    <div class="card-header upload-header">Upload Video</div>
                    <div class="card-body upload-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="video_file" class="form-label">File:</label>
                                <input type="file" name="video_file" class="form-control" accept="video/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Upload Video</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Uploaded Videos -->
            <div class="col-lg-6">
                <div class="card existing-videos-card">
                    <div class="card-header existing-videos-header">Existing Uploaded Videos</div>
                    <div class="card-body existing-videos-body">
                        <div class="row g-3">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="card video-card">
                                        <video class="card-img-top" controls>
                                            <source src="<?php echo $row['video_path']; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <div class="card-body text-center">
                                            <p class="video-name"><?php echo basename($row['video_path']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-hide messages after 3 seconds -->
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