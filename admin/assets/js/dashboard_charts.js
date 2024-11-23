document.addEventListener("DOMContentLoaded", function () {
    // Gender Impact Chart
    const ctxGenderImpact = document.getElementById('genderImpactChart');
    if (ctxGenderImpact) {
        new Chart(ctxGenderImpact.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Male', 'Female', 'Non-Binary'],
                datasets: [{
                    data: [40, 55, 5],
                    backgroundColor: ['#7a4ca1', '#b19cd9', '#d3bce3']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Gender Distribution Impact'
                    }
                }
            }
        });
    }

    // Program Impact Chart
    const ctxProgramImpact = document.getElementById('programImpactChart');
    if (ctxProgramImpact) {
        new Chart(ctxProgramImpact.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    'Leadership Training',
                    'Women Empowerment',
                    'Safe Spaces Act',
                    'Youth Advocacy'
                ],
                datasets: [{
                    label: 'Impact Score',
                    data: [85, 90, 75, 95],
                    backgroundColor: ['#9a73c7', '#b19cd9', '#cba7e5', '#7a4ca1']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Program Impact Analysis'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Attendees Over Time Chart
    const ctxAttendeesOverTime = document.getElementById('attendeesOverTimeChart');
    if (ctxAttendeesOverTime) {
        new Chart(ctxAttendeesOverTime.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['2019', '2020', '2021', '2022', '2023'],
                datasets: [{
                    label: 'Total Attendees',
                    data: [500, 700, 1000, 1800, 2540],
                    borderColor: '#7a4ca1',
                    backgroundColor: '#b19cd9',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Total Attendees Over Time'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // KPI Data for the four containers
    const kpiData = [
        { title: "Total Attendees", value: "2,545", bgColor: "bg-light-purple" },
        { title: "Programs Conducted", value: "125", bgColor: "bg-purple" },
        { title: "Impacted Communities", value: "45", bgColor: "bg-dark-purple" },
        { title: "Active Campaigns", value: "8", bgColor: "bg-light-purple" }
    ];

    // Target container
    const kpiContainer = document.getElementById("kpiCardsContainer");

    // Generate KPI Cards
    if (kpiContainer) {
        kpiData.forEach(kpi => {
            const card = document.createElement("div");
            card.className = "col-lg-3 col-md-6 mb-4";

            card.innerHTML = `
                <div class="card shadow-sm ${kpi.bgColor}">
                    <div class="card-body">
                        <h5 class="card-title">${kpi.title}</h5>
                        <h2 class="card-value">${kpi.value}</h2>
                    </div>
                </div>
            `;
            kpiContainer.appendChild(card);
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Community Reach Impact Chart
    const ctxCommunityReach = document.getElementById('communityReachChart');
    if (ctxCommunityReach) {
        new Chart(ctxCommunityReach.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Health', 'Education', 'Employment', 'Advocacy'],
                datasets: [
                    {
                        label: 'Communities Impacted',
                        data: [20, 35, 40, 25],
                        backgroundColor: ['#7a4ca1', '#b19cd9', '#cba7e5', '#9a73c7']
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Community Reach Impact'
                    }
                },
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    // Campaign Growth Trend Chart
    const ctxCampaignGrowth = document.getElementById('campaignGrowthChart');
    if (ctxCampaignGrowth) {
        new Chart(ctxCampaignGrowth.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['2019', '2020', '2021', '2022', '2023'],
                datasets: [
                    {
                        label: 'Campaigns Conducted',
                        data: [5, 10, 15, 20, 25],
                        borderColor: '#7a4ca1',
                        backgroundColor: '#b19cd9',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Campaign Growth Trend'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

window.addEventListener('resize', () => {
    Chart.helpers.each(Chart.instances, instance => {
        instance.resize();
    });
});

});
