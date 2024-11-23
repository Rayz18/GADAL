<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch pending programs
$programs_query = $conn->query("SELECT * FROM programs WHERE status = 'pending'");

// Check if the query was successful
if (!$programs_query) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pending Programs</title>
    <!-- Ensure the CSS path is correct -->
    <link rel="stylesheet" href="../../admin/assets/css/pending_programs.css">
</head>

<body>
    <h1>Pending Programs</h1>
    <div class="content-section">
        <?php while ($program = $programs_query->fetch_assoc()): ?>
            <div class="content-item">
                <p><strong><?php echo htmlspecialchars($program['program_name']); ?></strong></p>
                <form method="POST" action="moderate_content_action.php">
                    <input type="hidden" name="content_mdrtn_id" value="<?php echo $program['program_id']; ?>">
                    <input type="hidden" name="content_type" value="program">
                    <button type="submit" name="action" value="approve" class="approve">Approve</button>
                    <button type="submit" name="action" value="decline" class="decline">Decline</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>