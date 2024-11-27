<?php
session_start();
require_once '../../config/config.php';

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
    <link rel="stylesheet" href="../admin/assets/css/add_staff.css">
</head>

<body>
    <div class="container mt-5">
        <!-- Back Button -->
        <a href="manage_staff.php" class="btn btn-outline-secondary mb-3">← Back</a>

        <h2 class="text-center text-primary fw-bold">Add Staff Account</h2>
        <form action="add_staff.php" method="POST" class="bg-white p-5 rounded-4 shadow-lg mx-auto"
            style="max-width: 500px;">
            <div class="mb-3">
                <label for="staff_name" class="form-label text-secondary fw-bold">Staff Name:</label>
                <input type="text" id="staff_name" name="staff_name" class="form-control" placeholder="Enter full name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label text-secondary fw-bold">Username:</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label text-secondary fw-bold">Password:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Create Account</button>
        </form>
    </div>
</body>

</html>
