<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch the staff data if the staff_id is provided
$staff_id = $_GET['staff_id'] ?? null;
if ($staff_id) {
    $stmt = $conn->prepare("SELECT * FROM staff_accounts WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();
}

// Update the staff details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    $staff_name = $_POST['staff_name'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE staff_accounts SET staff_name = ?, username = ?, password = ? WHERE staff_id = ?");
        $stmt->bind_param("sssi", $staff_name, $username, $password, $staff_id);
    } else {
        $stmt = $conn->prepare("UPDATE staff_accounts SET staff_name = ?, username = ? WHERE staff_id = ?");
        $stmt->bind_param("ssi", $staff_name, $username, $staff_id);
    }

    $stmt->execute();
    $stmt->close();

    header('Location: manage_staff.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/assets/css/edit_staff.css">
</head>

<body>
    <div class="container mt-5">
        <a href="manage_staff.php" class="btn btn-outline-secondary mb-3">← Back</a>
        <h2 class="text-center text-primary fw-bold">Edit Staff Account</h2>
        <form action="edit_staff.php" method="POST" class="bg-white p-5 rounded-4 shadow-lg mx-auto"
            style="max-width: 500px;">
            <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff['staff_id']); ?>">
            <div class="mb-3">
                <label for="staff_name" class="form-label text-secondary fw-bold">Staff Name:</label>
                <input type="text" id="staff_name" name="staff_name" class="form-control"
                    value="<?php echo htmlspecialchars($staff['staff_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label text-secondary fw-bold">Username:</label>
                <input type="text" id="username" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($staff['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label text-secondary fw-bold">New Password (optional):</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Save Changes</button>
        </form>
    </div>
</body>

</html>
