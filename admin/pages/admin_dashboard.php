<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="../../admin/assets/js/dashboard_charts.js" defer></script>
    <link rel="stylesheet" href="../../admin/assets/css/admin_dashboard.css">
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include '../../includes/AdminNavBar.php'; ?>
        <div class="main-content">
            <div class="container mt-5">
                <h1 class="text-center">Admin Dashboard</h1>
                <div class="analytics-section mt-4">
                    <h2 class="text-center">Analytics and Visualizations</h2>
                    <div class="d-flex justify-content-center mt-3">
                        <div class="chart-container bg-light p-4 shadow-sm rounded">
                            <canvas id="staffChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../includes/assets/sidebarToggle.js"></script>
</body>

</html>