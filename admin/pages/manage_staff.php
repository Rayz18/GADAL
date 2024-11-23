<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$staff_accounts_query = $conn->query("SELECT * FROM staff_accounts");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/assets/css/manage_staff.css">
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include '../../includes/AdminNavBar.php'; ?>
        <div class="main-content">
            <div class="manage-staff-container container mt-5">
                <h1 class="text-center">MANAGE STAFF ACCOUNTS</h1>
                <div class="text-right mb-3">
                    <a href="add_staff.php" class="btn btn-primary add-staff">Add New Staff</a>
                </div>
                <div class="staff-list">
                    <table class="table table-striped table-hover shadow-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Staff Name</th>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($staff = $staff_accounts_query->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($staff['staff_name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['username']); ?></td>
                                    <td>
                                        <a href="edit_staff.php?staff_id=<?php echo $staff['staff_id']; ?>"
                                            class="btn btn-sm btn-info edit">Edit</a>
                                        <a href="delete_staff.php?staff_id=<?php echo $staff['staff_id']; ?>"
                                            class="btn btn-sm btn-danger delete">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../includes/assets/sidebarToggle.js"></script>
</body>

</html>