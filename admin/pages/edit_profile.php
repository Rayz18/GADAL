<?php
session_start();
require_once '../../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$profile_query = $conn->prepare("SELECT admin_name, username, email, contact_number, gender FROM admin_accounts WHERE admin_id = ?");
$profile_query->bind_param("i", $admin_id);
$profile_query->execute();
$profile_result = $profile_query->get_result();

if ($profile_result->num_rows > 0) {
    $admin_data = $profile_result->fetch_assoc();
} else {
    echo "Profile not found.";
    exit;
}

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['admin_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $gender = $_POST['gender'];

    $update_query = $conn->prepare("UPDATE admin_accounts SET admin_name = ?, username = ?, email = ?, contact_number = ?, gender = ? WHERE admin_id = ?");
    $update_query->bind_param("sssssi", $admin_name, $username, $email, $contact_number, $gender, $admin_id);

    if ($update_query->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        header('Location: profile.php');
        exit;
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../includes/assets/AdminNavBar.css">
</head>

<body>
    <?php include '../../public/includes/AdminNavBar.php'; ?>

    <div class="container" style="margin-top: 5px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Edit Profile</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="admin_name" class="form-label"><strong>Full Name</strong></label>
                                <input type="text" name="admin_name" id="admin_name" class="form-control"
                                       value="<?php echo htmlspecialchars($admin_data['admin_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Username</strong></label>
                                <input type="text" name="username" id="username" class="form-control"
                                       value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><strong>Email</strong></label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label"><strong>Contact Number</strong></label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control"
                                       value="<?php echo htmlspecialchars($admin_data['contact_number']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label"><strong>Gender</strong></label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="Male" <?php echo $admin_data['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $admin_data['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo $admin_data['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="profile.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>