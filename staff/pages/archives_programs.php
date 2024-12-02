<?php
session_start();
include '../../config/config.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: staff_login.php');
    exit;
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
    <style>
        .card-img-top {
            width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .placeholder-img {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
            font-size: 0.875rem;
            color: #6c757d;
            background-color: #e9ecef;
            border-radius: 5px;
        }
    </style>
</head>

<body class="bg-light-gray">
    <?php include '../../public/includes/StaffNavBar.php'; ?>

    <!-- Title Section -->
    <div class="bg-white text-center py-4 shadow-sm">
        <h1 class="fw-bold text-primary">Archived Programs</h1>
    </div>

    <!-- Archived Programs Container -->
    <div class="container my-5">
        <div class="row g-4">
            <?php
            if ($archived_programs_query->num_rows > 0) {
                while ($program = $archived_programs_query->fetch_assoc()) {
                    // Placeholder image if program_img is missing
                    $program_img = $program['program_img'] ? "../../staff/upload/" . htmlspecialchars($program['program_img']) : null;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($program_img): ?>
                                <img src="<?php echo $program_img; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($program['program_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-img">
                                    No Image Available
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($program['program_name']); ?></h5>
                                <form method="GET" action="restore_handler.php">
                                    <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Restore</button>
                                </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>