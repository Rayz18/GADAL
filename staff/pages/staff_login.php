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
                    <img src="../../assets/images/BSU.png" alt="University Logo" class="logo">
                    <img src="../../assets/images/GAD.png" alt="Department Logo" class="logo">
                </div>
                <h1 class="heading">Empowering Equality, Advancing Development</h1>
            </div>

            <div class="login-box">
                <h2 class="login-title">Staff Login</h2>
                <?php if (isset($error_message)): ?>
                    <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
                <?php endif; ?>
                <form action="staff_login.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="username" name="username" class="form-input" placeholder="Username"
                            required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Password"
                            required>
                    </div>
                    <button type="submit" class="submit-btn">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>