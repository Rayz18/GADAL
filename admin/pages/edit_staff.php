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
</head>

<body class="min-vh-100 d-flex justify-content-center align-items-center">

    <div class="container bg-white p-4 rounded shadow-lg">
        <h2 class="text-center mb-4">EDIT STAFF DETAILS</h2>

        <form action="edit_staff.php" method="POST">
            <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff['staff_id']); ?>">

            <div class="mb-3">
                <label for="staff_name" class="form-label fw-bold">Staff Name:</label>
                <input type="text" id="staff_name" name="staff_name" value="<?php echo htmlspecialchars($staff['staff_name']); ?>"
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($staff['username']); ?>"
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">New Password (optional):</label>
                <input type="password" id="password" name="password" placeholder="Enter new password"
                    class="form-control">
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
