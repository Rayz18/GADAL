<?php
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch staff details
    $stmt = $conn->prepare("SELECT * FROM staff_accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if ($staff && password_verify($password, $staff['password'])) {
        // Set session variables
        $_SESSION['staff_id'] = $staff['staff_id'];
        $_SESSION['staff_name'] = $staff['staff_name'];
        $_SESSION['staff_logged_in'] = true;

        header('Location: staff_dashboard.php'); // Redirect to staff dashboard
        exit;
    } else {
        $error_message = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="../../staff/assets/css/staff_login.css">
</head>

<body>
    <div class="container">
        <div class="flex-container">
            <div class="logo-text">
                <div class="logo-container">
                    <img src="../../public/assets/images/BSU.png" alt="University Logo" class="logo">
                    <img src="../../public/assets/images/GAD.png" alt="Department Logo" class="logo">
                </div>
                <h1 class="heading">Empowering Equality, Advancing Development</h1>
            </div>

            <div class="login-box">
                <h2 class="login-title">Staff Login</h2>
                <form action="staff_login.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="username" name="username" class="form-input" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Password" required>
                    </div>
                    <!-- Error message placed below the password field -->
                    <?php if (isset($error_message)): ?>
                        <p id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <div class="form-group mt-auto">
                        <button type="submit" class="submit-btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Automatically hide the error message after 3 seconds
        document.addEventListener("DOMContentLoaded", () => {
            const errorMessage = document.getElementById("error-message");
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = "none"; // Hide the error message
                }, 3000); // 3 seconds
            }
        });
    </script>
</body>

</html>