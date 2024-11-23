<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch staff name
$staff_name = $_SESSION['staff_name'] ?? 'Staff';
?>

    <!-- Header Section -->
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow">
        <div class="header-title"></div> <!-- Removed "Staff Dashboard" title -->
        <div class="header-actions d-flex align-items-center">
            <span class="me-3 text-dark">
                Welcome, <a href="profile.php" class="text-primary text-decoration-none"><strong><?php echo $staff_name; ?></strong></a>
            </span>
        </div>
    </header>
