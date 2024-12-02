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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../includes/assets/StaffNavBar.css">
    <link rel="stylesheet" href="../../staff/assets/css/profile.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">Staff Profile</div>
            <div class="profile-name">
                <?php echo htmlspecialchars($staff_data['staff_name']); ?>
            </div>
            <div class="profile-item">
                <i class="bi bi-person profile-icon"></i>
                <span class="profile-label">Username:</span>
                <span><?php echo htmlspecialchars($staff_data['username']); ?></span>
            </div>
            <div class="profile-item">
                <i class="bi bi-envelope profile-icon"></i>
                <span class="profile-label">Email:</span>
                <span><?php echo htmlspecialchars($staff_data['email']); ?></span>
            </div>
            <div class="profile-item">
                <i class="bi bi-telephone profile-icon"></i>
                <span class="profile-label">Contact Number:</span>
                <span><?php echo htmlspecialchars($staff_data['contact_number']); ?></span>
            </div>
            <div class="profile-item">
                <i class="bi bi-gender-ambiguous profile-icon"></i>
                <span class="profile-label">Gender:</span>
                <span><?php echo htmlspecialchars($staff_data['gender']); ?></span>
            </div>
            <div class="profile-footer">
                <a href="staff_dashboard.php" class="btn btn-secondary">Back</a>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>

</html>
