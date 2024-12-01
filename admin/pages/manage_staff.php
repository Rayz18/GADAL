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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/assets/css/manage_staff.css">
</head>

<body>
<?php include '../../public/includes/AdminNavBar.php'; ?>
<?php include '../../public/includes/AdminHeader.php'; ?>
    <!-- Notification Section -->
    <div id="notification-container" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; width: 90%; max-width: 500px;">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="layout">
        <!-- Sidebar -->
        <div id="toggle-sidebar" class="toggle-sidebar">
            <!-- Sidebar content can go here -->
        </div>
        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Toggle Sidebar Icon -->
            <div id="toggle-sidebar" class="toggle-sidebar"></div>
            <h1 class="learning-title text-primary text-center">Manage Staff Account</h1>

        <div class="text-end mb-3">
    <a href="add_staff.php" class="btn btn-success">+ Add Staff</a>
</div>

        <div class="staff-list table-responsive">
            <table class="table table-striped align-middle table-hover">
                <thead class="table-success">
                    <tr>
                        <th scope="col">Staff Name</th>
                        <th scope="col">Username</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($staff = $staff_accounts_query->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($staff['staff_name']); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($staff['username']); ?></td>
                            <td class="text-center">
                                <a href="edit_staff.php?staff_id=<?php echo $staff['staff_id']; ?>"
                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="javascript:void(0);"
                                    class="btn btn-sm btn-danger delete-button"
                                    data-href="delete_staff.php?staff_id=<?php echo $staff['staff_id']; ?>">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Attach click event to delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                const deleteUrl = this.getAttribute('data-href');
                const confirmDelete = confirm("Are you sure you want to delete this staff account?");
                if (confirmDelete) {
                    window.location.href = deleteUrl;
                }
            });
        });

        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            const notifications = document.querySelectorAll('.alert');
            notifications.forEach(notification => {
                notification.classList.add('fade');
                setTimeout(() => notification.remove(), 150);
            });
        }, 5000);
    </script>
    <script>document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const toggleButton = document.getElementById("toggle-sidebar");

    toggleButton.addEventListener("click", function () {
        if (sidebar.classList.contains("open")) {
            // Close the sidebar
            sidebar.classList.remove("open");
            content.classList.remove("shifted");
        } else {
            // Open the sidebar
            sidebar.classList.add("open");
            content.classList.add("shifted");
        }
    });
});</script>
</body>

</html>
