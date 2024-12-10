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
    $program_img = $_FILES['program_img'];

    // Validate the input
    if (empty($program_name) || empty($program_img['name'])) {
        $error = "All fields are required.";
    } else {
        // Process the image upload
        $target_dir = "../../uploads/"; // Directory where images will be stored
        $target_file = $target_dir . basename($program_img["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($program_img["tmp_name"]);
        if ($check === false) {
            $error = "The file is not an image.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($program_img["tmp_name"], $target_file)) {
                // Update the program in the database
                $update_query = $conn->prepare("UPDATE programs SET program_name = ?, program_img = ? WHERE program_id = ?");
                $update_query->bind_param('ssi', $program_name, $target_file, $program_id);

                if ($update_query->execute()) {
                    // Redirect to manage_programs.php after successful update
                    header('Location: manage_programs.php?success=1');
                    exit;
                } else {
                    $error = "Failed to update the program. Please try again.";
                }
            } else {
                $error = "Sorry, there was an error uploading the file.";
            }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Add Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display success or error messages -->
                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>

                    <!-- Edit Program Form -->
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="program_name" class="form-label">Program Name</label>
                            <input type="text" class="form-control" id="program_name" name="program_name"
                                value="<?php echo htmlspecialchars($program['program_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="program_img" class="form-label">Program Image</label>
                            <input type="file" class="form-control" id="program_img" name="program_img" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Trigger the modal (button) -->
    <script>
        // Open the modal as soon as the page loads
        $(document).ready(function () {
            $('#editProgramModal').modal('show');
        });

        // Handle form submission via AJAX for a seamless experience
        $('#saveChangesBtn').on('click', function () {
            var form = $('form');
            var formData = new FormData(form[0]);

            $.ajax({
                url: '', // Current page (it will process the form data)
                type: 'POST',
                data: formData,
                processData: false, // Important for file uploads
                contentType: false, // Important for file uploads
                success: function (response) {
                    // If the update is successful, reload the page to show the changes
                    location.reload();
                },
                error: function () {
                    alert('There was an error updating the program.');
                }
            });
        });
    </script>
</body>

</html>