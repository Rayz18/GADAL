<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle the form submission to add a new staff account
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_name = $_POST['staff_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO staff_accounts (staff_name, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $staff_name, $username, $password);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Staff account created successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to create staff account.";
    }

    $stmt->close();

    // Redirect to manage_staff.php
    header('Location: manage_staff.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/assets/css/add_staff.css">
</head>

<body class="min-vh-100 d-flex justify-content-center align-items-center">
    <div class="container">
        <h2 class="page-title">Add Staff Account</h2>
        <form action="add_staff.php" method="POST">
            <div class="mb-3">
                <label for="staff_name" class="form-label fw-bold">Staff Name:</label>
                <input type="text" id="staff_name" name="staff_name" placeholder="Enter full name" 
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter username" 
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password" 
                    class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="manage_staff.php" class="btn btn-link">Back</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
