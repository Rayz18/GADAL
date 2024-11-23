<?php
session_start();
require_once '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch staff details from session or database
$staff_id = $_SESSION['staff_id']; // Assuming staff_id is stored in the session
$profile_query = $conn->prepare("SELECT staff_name, username, email, contact_number, gender FROM staff_accounts WHERE staff_id = ?");
$profile_query->bind_param("i", $staff_id);
$profile_query->execute();
$profile_result = $profile_query->get_result();

if ($profile_result->num_rows > 0) {
    $staff_data = $profile_result->fetch_assoc();
} else {
    echo "Profile not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../includes/assets/StaffNavBar.css">
</head>

<body>
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Staff Profile</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Full Name:</strong></label>
                            <p><?php echo htmlspecialchars($staff_data['staff_name']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Username:</strong></label>
                            <p><?php echo htmlspecialchars($staff_data['username']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p><?php echo htmlspecialchars($staff_data['email']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Contact Number:</strong></label>
                            <p><?php echo htmlspecialchars($staff_data['contact_number']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Gender:</strong></label>
                            <p><?php echo htmlspecialchars($staff_data['gender']); ?></p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="staff_dashboard.php" class="btn btn-secondary">Back</a>
                        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
