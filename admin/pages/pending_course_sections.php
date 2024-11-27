<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$sections_query = $conn->query("SELECT * FROM course_sections WHERE status = 'pending'");

if (!$sections_query) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pending Course Sections</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../../public/includes/AdminNavBar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Pending Course Sections</h1>
        <div class="list-group mt-4">
            <?php while ($section = $sections_query->fetch_assoc()): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <strong><?php echo htmlspecialchars($section['section_name']); ?></strong>
                    <div>
                        <form action="moderate_content_action.php" method="post">
                            <input type="hidden" name="content_mdrtn_id" value="<?php echo $row[$columns[0]]; ?>">
                            <input type="hidden" name="content_type" value="<?php echo strtolower($content_type); ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                            <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                        </form>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>