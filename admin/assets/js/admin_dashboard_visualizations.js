window.onload = function() {
    // Fetch data when the page loads
    fetchDashboardData();
};

function fetchDashboardData() {
    // Fetch learners per course
    fetch('admin_dashboard_queries.php?action=getLearnersPerCourse')
        .then(response => response.json())
        .then(data => {
            createLearnersPerCourseChart(data);
        })
        .catch(error => console.error('Error fetching learners per course data:', error));

    // Fetch proportion of courses offered per program
    fetch('admin_dashboard_queries.php?action=getCoursesPerProgram')
        .then(response => response.json())
        .then(data => {
            createCoursesPerProgramChart(data);
        })
        .catch(error => console.error('Error fetching courses per program data:', error));

    // Fetch enrollment trends over time
    fetch('admin_dashboard_queries.php?action=getEnrollmentTrends')
        .then(response => response.json())
        .then(data => {
            createEnrollmentTrendsChart(data);
        })
        .catch(error => console.error('Error fetching enrollment trends data:', error));

    // Fetch average pre-test and post-test scores
    fetch('admin_dashboard_queries.php?action=getTestScoresPerCourse')
        .then(response => response.json())
        .then(data => {
            createTestScoresChart(data);
        })
        .catch(error => console.error('Error fetching test scores data:', error));

    // Fetch average quiz scores per course
    fetch('admin_dashboard_queries.php?action=getQuizScoresPerCourse')
        .then(response => response.json())
        .then(data => {
            createQuizScoresChart(data);
        })
        .catch(error => console.error('Error fetching quiz scores data:', error));

    // Fetch gender enrollment data
    fetch('admin_dashboard_queries.php?action=getGenderEnrollmentPerCourse')
        .then(response => response.json())
        .then(data => {
            createGenderEnrollmentChart(data);
        })
        .catch(error => console.error('Error fetching gender enrollment data:', error));
}

// Create Learners Enrolled per Course Chart
function createLearnersPerCourseChart(data) {
    var ctx = document.getElementById('enrolledPerCourseChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.course_name),
            datasets: [{
                label: 'Learners Enrolled',
                data: data.map(item => item.total_enrolled),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.raw + ' learners';
                        }
                    }
                }
            }
        }
    });
}

// Create Proportion of Courses Offered per Program Chart
function createCoursesPerProgramChart(data) {
    var ctx = document.getElementById('coursesPerProgramChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.program_name),
            datasets: [{
                data: data.map(item => item.total_courses),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

// Create Enrollment Trends Over Time Chart
function createEnrollmentTrendsChart(data) {
    var ctx = document.getElementById('enrollmentTrendsChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.month),
            datasets: [{
                label: 'Total Enrolled',
                data: data.map(item => item.total_enrolled),
                borderColor: '#36A2EB',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Enrolled'
                    }
                }
            }
        }
    });
}

// Create Average Pre-test and Post-test Scores per Course Chart
function createTestScoresChart(data) {
    var ctx = document.getElementById('testScoresChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.course_name),
            datasets: [
                {
                    label: 'Average Pre-test Score',
                    data: data.map(item => item.avg_pre_test),
                    backgroundColor: '#FF6384'
                },
                {
                    label: 'Average Post-test Score',
                    data: data.map(item => item.avg_post_test),
                    backgroundColor: '#36A2EB'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

// Create Average Quiz Scores per Course Chart
function createQuizScoresChart(data) {
    var ctx = document.getElementById('quizScoresChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.course_name),
            datasets: [{
                label: 'Average Quiz Score',
                data: data.map(item => item.avg_quiz_score),
                backgroundColor: '#FFCE56'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

// Create Number of Male and Female Enrolled in Each Course Chart
function createGenderEnrollmentChart(data) {
    var ctx = document.getElementById('genderEnrollmentChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.course_name),
            datasets: [
                {
                    label: 'Male',
                    data: data.map(item => item.male_count),
                    backgroundColor: '#FF6384'
                },
                {
                    label: 'Female',
                    data: data.map(item => item.female_count),
                    backgroundColor: '#36A2EB'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}
