<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
}       

// Handle program deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['program_id'])) {
    $program_id = intval($_POST['program_id']);  
    
    // Prepare and execute delete query
    $delete_query = $conn->prepare("DELETE FROM programs WHERE program_id = ?");
    $delete_query->bind_param("i", $program_id);

    if ($delete_query->execute()) {
        // Set a success message
        $message = "Program deleted successfully.";
    } else {
        // Set an error message
        $error = "Failed to delete program.";
    }

    $delete_query->close();
}

// Fetch archived programs
$archived_programs_query = $conn->query("
    SELECT program_id, program_name, program_img 
    FROM programs 
    WHERE archive = TRUE
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Programs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../staff/assets/css/archives_programs.css">
</head>


<body>
    <div class="layout">
        <!-- Sidebar -->
        <div id="toggle-sidebar" class="toggle-sidebar">
            <!-- Sidebar content -->
        </div>
        <?php include '../../public/includes/StaffNavBar.php'; ?>
        <?php include '../../public/includes/header.php'; ?>
        <!-- Main Content -->
        <div id="content" class="content">
            <div id="toggle-sidebar" class="toggle-sidebar"></div>

            <!-- Page Title Section -->
            <div class="page-title-container">
                <h1 class="page-title">ARCHIVED PROGRAMS</h1>
            </div>

            <!-- Toast Notification -->
            <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
                <?php if (!empty($message)): ?>
                    <div id="success-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div id="error-toast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Archived Programs Container -->
            <div class="container my-5">
                <div class="row g-4">
                    <?php
                    if ($archived_programs_query->num_rows > 0) {
                        while ($program = $archived_programs_query->fetch_assoc()) {
                            $program_img = $program['program_img'] ? "../../staff/upload/" . htmlspecialchars($program['program_img']) : null;
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <?php if ($program_img): ?>
                                        <img src="<?php echo $program_img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($program['program_name']); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-img">
                                            No Image Available
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($program['program_name']); ?></h5>
                                        <div class="d-flex justify-content-end gap-2">
                                            <form method="GET" action="restore_handler.php" class="d-inline">
                                                <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                <button type="submit" class="btn btn-warning btn-sm">Restore</button>
                                            </form>
                                            <form method="POST" action="" class="d-inline" onsubmit="return confirmDelete();">
                                                <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center text-muted'>No archived programs available.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this program? This action cannot be undone.");
        }

        document.addEventListener("DOMContentLoaded", function () {
            const successToast = document.getElementById('success-toast');
            const errorToast = document.getElementById('error-toast');
            if (successToast) {
                const toast = new bootstrap.Toast(successToast);
                toast.show();
            }
            if (errorToast) {
                const toast = new bootstrap.Toast(errorToast);
                toast.show();
            }

            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            const toggleButton = document.getElementById("toggle-sidebar");
            toggleButton.addEventListener("click", function () {
                if (sidebar.classList.contains("open")) {
                    sidebar.classList.remove("open");
                    content.classList.remove("shifted");
                } else {
                    sidebar.classList.add("open");
                    content.classList.add("shifted");
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 