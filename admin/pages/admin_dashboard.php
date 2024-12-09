<?php
?>




<!DOCTYPE html>
<html lang="en">




<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5.3.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>




<body>
    <div class="container mt-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        <div class="row">
            <!-- Learners Enrolled per Course -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Learners Enrolled per Course</h5>
                        <canvas id="enrolledPerCourseChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Proportion of Courses Offered per Program -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Proportion of Courses Offered per Program</h5>
                        <canvas id="coursesPerProgramChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Enrollment Trends Over Time -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Enrollment Trends Over Time</h5>
                        <canvas id="enrollmentTrendsChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Average Pre-test and Post-test Scores per Course -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Average Pre-test and Post-test Scores per Course</h5>
                        <canvas id="testScoresChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Average Quiz Scores per Course -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Average Quiz Scores per Course</h5>
                        <canvas id="quizScoresChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Number of Male and Female Enrolled in Each Course -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Male and Female Enrolled in Each Course</h5>
                        <canvas id="genderEnrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Include the JavaScript files -->
    <script src="../../admin/assets/js/admin_dashboard_visualizations.js"></script>
</body>




</html>