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
        <!-- Admin NavBar -->
        <?php include '../../public/includes/AdminNavBar.php'; ?>

        <div class="main-content container mt-4">
            <h1 class="text-center mb-5">Admin Dashboard</h1>

            <!-- KPI Cards -->
            <div class="row" id="kpiCardsContainer">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Attendees</h5>
                            <h2 class="card-value" id="totalAttendees">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Registered in Seminar</h5>
                            <h2 class="card-value" id="registeredSeminar">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Evaluations</h5>
                            <h2 class="card-value" id="totalEvaluations">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Active Programs</h5>
                            <h2 class="card-value" id="activePrograms">0</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mt-4">
                <!-- Pie Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="evaluationRatingsChart"></canvas>
                    </div>
                </div>

                <!-- Line Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="testScoresChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Vertical Bar Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="genderImpactChart"></canvas>
                    </div>
                </div>

                <!-- Horizontal Bar Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-container bg-light p-3 shadow-sm rounded">
                        <canvas id="programImpactChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../includes/assets/js/sidebarToggle.js"></script>
</body>

</html>
