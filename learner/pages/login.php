<?php
// Include database connection
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists
    $sql = "SELECT learner_id, password FROM learners WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['learner_id'] = $row['learner_id'];
            header('Location: /GADAL/Home.php');
            exit;
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No account found with that email!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../learner/assets/css/login.css">
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
                <h2 class="login-title">Log in</h2>
                <form action="#" method="POST">
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-input" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Password"
                            required>
                    </div>
                    <div class="error-message-container">
                        <?php if (isset($error_message)): ?>
                            <p class="error-message"><?= $error_message ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-footer">
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" class="submit-btn">Login</button>
                </form>
                <p class="signup-text">
                    Don’t have an account? <a href="sign-up.php" class="signup-link">Create account</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>