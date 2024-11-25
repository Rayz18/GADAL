<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch admin name
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>

<!-- Header Section -->
<header class="admin-header d-flex justify-content-between align-items-center p-3 shadow-sm">
    <div class="header-title">
        <!-- Optionally add a logo or dashboard title here if needed -->
    </div>
    <div class="header-actions d-flex align-items-center">
        <span class="me-3 text-dark">
            Welcome, 
            <a href="profile.php" class="text-primary text-decoration-none">
                <strong><?php echo htmlspecialchars($admin_name); ?></strong>
            </a>
        </span>
    </div>
</header>

<style>
    header {
    background-color: #f8f9fa; /* Light gray background */
    border-bottom: 2px solid #B19CD9; /* Light purple border */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

.header-actions {
    display: flex;
    align-items: center; /* Vertically aligns items */
}

.header-actions span {
    font-size: 1rem; /* Regular text size */
    color: #333; /* Dark gray for text */
    margin-right: 15px; /* Space between text and next element */
}

.header-actions .btn {
    padding: 0.4rem 1rem; /* Button padding for a balanced look */
    font-size: 0.9rem; /* Slightly smaller font size for buttons */
}

</style>
