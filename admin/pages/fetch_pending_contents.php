<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$content_type = $_GET['type'] ?? null;
if (!$content_type) {
    echo "No content type specified.";
    exit;
}

$content_queries = [
    "Programs" => "SELECT * FROM programs WHERE status = 'pending'",
    "Courses" => "SELECT * FROM courses WHERE status = 'pending'",
    "Sections" => "SELECT * FROM course_sections WHERE status = 'pending'",
    "Learning Materials" => "SELECT * FROM learning_materials WHERE status = 'pending'",
    "Course Videos" => "SELECT * FROM course_videos WHERE status = 'pending'",
    "Post-Test Questions" => "SELECT * FROM post_test_questions WHERE status = 'pending'",
    "Pre-Test Questions" => "SELECT * FROM pre_test_questions WHERE status = 'pending'",
    "Seminars" => "SELECT * FROM seminars WHERE status = 'pending'",
];

$query = $content_queries[$content_type] ?? null;
if (!$query) {
    echo "Invalid content type.";
    exit;
}

$result = $conn->query($query);
if (!$result) {
    echo "Failed to fetch content.";
    exit;
}

// Check if there are any rows in the result set
$columns = [];
if ($result->num_rows > 0) {
    $columns = array_keys($result->fetch_assoc());
    $result->data_seek(0); // Reset the pointer to the beginning of the result set
}

$image_columns = ['program_img', 'course_img', 'material_path', 'poster_path'];
$video_columns = ['video_path', 'course_video_path'];
$file_columns = ['file_path'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Pending <?php echo htmlspecialchars($content_type); ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../../includes/AdminNavBar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Pending <?php echo htmlspecialchars($content_type); ?></h1>
        <div class="table-responsive mt-4">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                            <?php endforeach; ?>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <td>
                                        <?php
                                        if (in_array(strtolower($column), $image_columns) && !empty($row[$column])): ?>
                                            <img src="../../staff/upload/images/<?php echo htmlspecialchars($row[$column]); ?>"
                                                alt="Image" class="img-thumbnail" style="max-width: 100px;">
                                        <?php elseif (in_array(strtolower($column), $video_columns) && !empty($row[$column])): ?>
                                            <video width="150" height="100" controls>
                                                <source src="../../staff/upload/videos/<?php echo htmlspecialchars($row[$column]); ?>"
                                                    type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($row[$column]); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <form action="moderate_content_action.php" method="post" class="d-inline">
                                        <input type="hidden" name="content_mdrtn_id" value="<?php echo $row[$columns[0]]; ?>">
                                        <input type="hidden" name="content_type"
                                            value="<?php echo strtolower($content_type); ?>">
                                        <button type="submit" name="action" value="approve"
                                            class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="action" value="decline"
                                            class="btn btn-danger btn-sm">Decline</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No pending <?php echo htmlspecialchars($content_type); ?> found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>