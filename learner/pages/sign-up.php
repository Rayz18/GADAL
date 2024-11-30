<?php
// Include database connection
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password length and complexity
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^\w]/', $password)) {
        echo "Password must be at least 8 characters long, contain letters, numbers, and special characters.";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query
    $sql = "INSERT INTO learners (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        header('Location: login.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../../learner/assets/css/sign-up.css">
    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm-password').value;
            const error_message = document.getElementById('password-error');

            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

            if (!passwordRegex.test(password)) {
                error_message.textContent = 'Password must be at least 8 characters long, contain letters, numbers, and special characters.';
                return false;
            }

            if (password !== confirm_password) {
                error_message.textContent = 'Passwords do not match!';
                return false;
            }

            error_message.textContent = '';
            return true;
        }
    </script>
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

            <div class="signup-box">
                <h2 class="signup-title">Create Account</h2>
                <form action="#" method="POST" onsubmit="return validatePassword();">
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-input email-input"
                            placeholder="Email Address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="first-name" name="first_name" class="form-input"
                                placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" id="last-name" name="last_name" class="form-input"
                                placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="form-group password-group">
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="Create Password" required>
                    </div>
                    <div class="form-group password-group">
                        <input type="password" id="confirm-password" name="confirm_password" class="form-input"
                            placeholder="Confirm Password" required>
                    </div>
                    <p id="password-error" style="color: red; font-size: 14px;"></p> <!-- Password error message -->
                    <button type="submit" class="submit-btn">Sign up</button>
                </form>
                <p class="login-footer-text">
                    Already have an account? <a href="login.php" class="login-link">Login</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
