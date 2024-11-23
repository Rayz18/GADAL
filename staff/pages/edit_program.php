<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Check if the program_id is provided in the URL
if (!isset($_GET['program_id']) || empty($_GET['program_id'])) {
    header('Location: manage_programs.php'); // Redirect if no program_id is provided
    exit;
}

$program_id = $_GET['program_id'];

// Fetch the program details from the database
$query = $conn->prepare("SELECT * FROM programs WHERE program_id = ?");
$query->bind_param('i', $program_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    header('Location: manage_programs.php'); // Redirect if the program doesn't exist
    exit;
}

$program = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = trim($_POST['program_name']);
    $program_desc = trim($_POST['program_desc']);

    // Validate the input
    if (empty($program_name) || empty($program_desc)) {
        $error = "All fields are required.";
    } else {
        // Update the program in the database
        $update_query = $conn->prepare("UPDATE programs SET program_name = ?, program_desc = ? WHERE program_id = ?");
        $update_query->bind_param('ssi', $program_name, $program_desc, $program_id);

        if ($update_query->execute()) {
            // Redirect to manage_programs.php after successful update
            header('Location: manage_programs.php?success=1');
            exit;
        } else {
            $error = "Failed to update the program. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../staff/assets/css/manage_programs.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Edit Program</h1>

        <!-- Display success or error messages -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <!-- Edit Program Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="program_name">Program Name</label>
                <input type="text" class="form-control" id="program_name" name="program_name"
                    value="<?php echo htmlspecialchars($program['program_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="program_desc">Program Description</label>
                <textarea class="form-control" id="program_desc" name="program_desc" rows="5"
                    required><?php echo htmlspecialchars($program['program_desc']); ?></textarea>
            </div>
            <div class="text-right">
                <a href="manage_programs.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>