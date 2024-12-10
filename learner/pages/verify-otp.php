<?php
// Include database connection
include '../../config/config.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    // Check if the OTP is valid
    if ($input_otp == $_SESSION['otp']) {
        // OTP is correct
        $learner_id = $_SESSION['learner_id'];

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the password in the database
        $sql = "UPDATE learners SET password = ? WHERE learner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $learner_id);

        if ($stmt->execute()) {
            // Password updated successfully, redirect to login page
            session_unset(); // Clear session data
            session_destroy(); // Destroy the session
            header('Location: login.php');
            exit;
        } else {
            $error_message = "Error resetting password!";
        }

        $stmt->close();
    } else {
        $error_message = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../../learner/assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2 class="login-title">Verify OTP</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <input type="text" id="otp" name="otp" class="form-input" placeholder="Enter OTP" required>
                </div>
                <div class="form-group">
                    <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Enter New Password" required>
                </div>
                <div class="error-message-container">
                    <?php if (isset($error_message)): ?>
                        <p class="error-message"><?= $error_message ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
