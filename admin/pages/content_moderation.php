<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$content_types = [
    "Programs" => "SELECT * FROM programs WHERE status = 'pending'",
    "Courses" => "SELECT * FROM courses WHERE status = 'pending'",
    "Sections" => "SELECT * FROM course_sections WHERE status = 'pending'",
    "Learning Materials" => "SELECT * FROM learning_materials WHERE status = 'pending'",
    "Course Videos" => "SELECT * FROM course_videos WHERE status = 'pending'",
    "Post-Test Questions" => "SELECT * FROM post_test_questions WHERE status = 'pending'",
    "Pre-Test Questions" => "SELECT * FROM pre_test_questions WHERE status = 'pending'",
    "Seminars" => "SELECT * FROM seminars WHERE status = 'pending'",
];

$content_data = [];
foreach ($content_types as $type => $query) {
    $result = $conn->query($query);
    $content_data[$type] = $result->num_rows;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Content Moderation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/assets/css/content_moderation.css">
    <style>
        /* Additional styles for dynamic color coding */
        .custom-card {
            transition: background-color 0.3s ease;
        }

        .pending {
            background-color: #ffcccc; /* Light red for pending items */
            border-color: #ff6b6b; /* Darker red for border */
        }

        .no-pending {
            background-color: #ccffcc; /* Light green for no pending items */
            border-color: #6bff6b; /* Darker green for border */
        }

        .card-text {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include '../../public/includes/AdminNavBar.php'; ?>
        <div class="main-content">
            <div class="content-moderation-container container mt-5">
                <h1 class="text-center">Content Moderation</h1>
                <div class="card-container">
                    <?php foreach ($content_data as $type => $count): ?>
                        <div class="custom-card card text-center mb-4 shadow-sm 
                            <?php echo $count > 0 ? 'pending' : 'no-pending'; ?>">
                            <div class="card-body">
                                <h5 class="card-title">Pending <?php echo htmlspecialchars($type); ?></h5>
                                <p class="card-text">
                                    <?php echo $count; ?> 
                                    <?php echo $count === 1 ? 'Pending ' . htmlspecialchars($type) : 'Pending ' . htmlspecialchars($type) . 's'; ?>
                                </p>
                                <a href="review_pending_content.php?type=<?php echo urlencode($type); ?>" 
                                   class="btn btn-primary view-button">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../includes/assets/sidebarToggle.js"></script>
</body>

</html>
