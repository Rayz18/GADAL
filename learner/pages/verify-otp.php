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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../learner/assets/css/login.css">
    <link rel="stylesheet" href="../../learner/assets/css/verify-otp.css">
</head>

<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row justify-content-between align-items-center w-100">
        <!-- Logo and Heading Section (Left) -->
        <div class="col-md-6 d-flex flex-column align-items-start p-3">
            <div class="d-flex mb-3">
                <img src="../../public/assets/images/BSU.png" alt="University Logo" class="logo me-2">
                <img src="../../public/assets/images/GAD.png" alt="Department Logo" class="logo">
            </div>
            <h1 class="text-white display-4 fw-bold quote-text">Empowering Equality, Advancing Development</h1>
        </div>

        <!-- OTP Verification Form Section (Right) -->
        <div class="col-md-4">
            <div class="card login-card p-4 shadow-sm d-flex flex-column justify-content-between">
                <h2 class="text-center mb-4">Verify OTP</h2>
                <form action="verify-otp.php" method="POST" class="d-flex flex-column h-100">
                    <div class="mb-3">
                        <input type="text" id="otp" name="otp" class="form-control" placeholder="Enter OTP" required>
                    </div>
                    <!-- Error message placed below the input field -->
                    <?php if (!empty($error_message)): ?>
                        <p id="error-message" class="text-danger error-message"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary w-100 mt-auto">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Automatically hide the error message after 4 seconds
    document.addEventListener("DOMContentLoaded", () => {
        const errorMessage = document.getElementById("error-message");
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.display = "none";
            }, 3000); // 3 seconds
        }
    });
</script>
</body>

</html>