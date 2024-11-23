<?php
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin_accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['admin_name'];

        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../admin/assets/css/admin_login.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row justify-content-between align-items-center w-100">
            <!-- Logo and Heading Section (Left) -->
            <div class="col-md-6 d-flex flex-column align-items-start p-3">
                <div class="d-flex mb-3">
                    <img src="../../assets/images/BSU.png" alt="University Logo" class="logo mr-2">
                    <img src="../../assets/images/GAD.png" alt="Department Logo" class="logo">
                </div>
                <h1 class="text-white display-4 font-weight-bold quote-text">Empowering Equality, Advancing Development
                </h1>
            </div>

            <!-- Login Form Section (Right) -->
            <div class="col-md-4">
                <div class="card login-card p-4 shadow-sm d-flex flex-column justify-content-between">
                    <h2 class="text-center mb-4">Admin Login</h2>
                    <?php if (!empty($error_message)): ?>
                        <p class="text-danger text-center"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <form action="admin_login.php" method="POST"
                        class="d-flex flex-column justify-content-between h-100">
                        <div class="form-group">
                            <input type="text" id="username" name="username" class="form-control" placeholder="Username"
                                required>
                        </div>
                        <div class="form-group mb-4">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mt-auto">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>