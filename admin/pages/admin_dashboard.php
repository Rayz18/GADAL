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
        <?php include '../../includes/AdminNavBar.php'; ?>
        <div class="main-content container">
            <h1 class="text-center mt-4 mb-5">Admin Dashboard</h1>

            <!-- KPI Cards Container -->
            <div id="kpiCardsContainer" class="row"></div>

            <div class="row mt-4">
    <!-- Gender Distribution Impact -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="genderImpactChart"></canvas>
        </div>
    </div>

    <!-- Program Impact Analysis -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="programImpactChart"></canvas>
        </div>
    </div>

    <!-- Total Attendees Over Time -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="attendeesOverTimeChart"></canvas>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Community Reach Impact -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="communityReachChart"></canvas>
        </div>
    </div>

    <!-- Campaign Growth Trend -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="campaignGrowthChart"></canvas>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Programs by Categories -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="chart-container bg-light p-3 shadow-sm rounded">
            <canvas id="programsByCategoryChart"></canvas>
        </div>
    </div>
</div>

        </div>
    </div>
    <script src="../../../includes/assets/sidebarToggle.js"></script>
</body>

</html>
