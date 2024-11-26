document.addEventListener("DOMContentLoaded", function () {
    // Sample data for demonstration purposes
    const sampleData = {
        totalAttendees: 200,
        registeredSeminar: 150,
        totalEvaluations: 120,
        activePrograms: 10,
        evaluationRatings: {
            labels: ['Poor', 'Fair', 'Good', 'Excellent'],
            data: [10, 20, 30, 40]
        },
        testScores: {
            labels: ['Module 1', 'Module 2', 'Module 3'],
            preTest: [60, 70, 75],
            postTest: [80, 85, 90]
        },
        genderDistribution: {
            labels: ['Male', 'Female', 'Prefer not to say'],
            data: [40, 50, 10]
        },
        programImpact: {
            labels: ['Program A', 'Program B', 'Program C'],
            data: [80, 70, 60]
        }
    };

    // Populate KPI Cards
    document.getElementById('totalAttendees').textContent = sampleData.totalAttendees;
    document.getElementById('registeredSeminar').textContent = sampleData.registeredSeminar;
    document.getElementById('totalEvaluations').textContent = sampleData.totalEvaluations;
    document.getElementById('activePrograms').textContent = sampleData.activePrograms;

    // Pie Chart: Evaluation Ratings
    new Chart(document.getElementById('evaluationRatingsChart'), {
        type: 'pie',
        data: {
            labels: sampleData.evaluationRatings.labels,
            datasets: [{
                data: sampleData.evaluationRatings.data,
                backgroundColor: ['#ff7b7b', '#ffcc7b', '#7bcc7b', '#7b7bff']
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Evaluation Ratings Distribution'
                }
            }
        }
    });

    // Line Chart: Pre-test vs Post-test Scores
    new Chart(document.getElementById('testScoresChart'), {
        type: 'line',
        data: {
            labels: sampleData.testScores.labels,
            datasets: [
                {
                    label: 'Pre-test',
                    data: sampleData.testScores.preTest,
                    borderColor: '#7a4ca1',
                    backgroundColor: '#b19cd9',
                    fill: true
                },
                {
                    label: 'Post-test',
                    data: sampleData.testScores.postTest,
                    borderColor: '#4ca17a',
                    backgroundColor: '#7bd9b1',
                    fill: true
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Pre-test vs Post-test Scores'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Vertical Bar Chart: Gender Distribution
    new Chart(document.getElementById('genderImpactChart'), {
        type: 'bar',
        data: {
            labels: sampleData.genderDistribution.labels,
            datasets: [{
                data: sampleData.genderDistribution.data,
                backgroundColor: ['#7a4ca1', '#b19cd9', '#d3bce3']
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Gender Distribution Impact'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Horizontal Bar Chart: Program Impact Analysis
    new Chart(document.getElementById('programImpactChart'), {
        type: 'bar',
        data: {
            labels: sampleData.programImpact.labels,
            datasets: [{
                label: 'Impact Score',
                data: sampleData.programImpact.data,
                backgroundColor: ['#7bcc7b', '#7b7bff', '#ffcc7b']
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                title: {
                    display: true,
                    text: 'Program Impact Analysis'
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
});
 
