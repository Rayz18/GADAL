<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/assets/css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js" defer></script>
    <script src="../../admin/assets/js/dashboard_charts.js" defer></script>
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include '../../public/includes/AdminNavBar.php'; ?>
        <?php include '../../public/includes/AdminHeader.php'; ?>
        <div class="main-content container">
            <h1 class="text-center mt-4 mb-5">Admin Dashboard</h1>

            <!-- Dashboard Content -->
            <div id="kpiCardsContainer" class="row"></div>

            <div class="row mt-4">
                <!-- Charts -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="genderImpactChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="programImpactChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="attendeesOverTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../includes/assets/js/sidebarToggle.js"></script>
</body>

</html>
