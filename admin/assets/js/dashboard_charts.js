document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('staffChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Staff A', 'Staff B', 'Staff C'], // Replace with dynamic data as needed
            datasets: [{
                label: 'Staff Performance',
                data: [12, 19, 3], // Replace with dynamic data as needed
                backgroundColor: '#b19cd9',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Staff Performance Analysis'
                }
            }
        }
    });
});
