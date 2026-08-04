document.addEventListener('DOMContentLoaded', function () {

    const chart = document.getElementById('dashboardChart');

    if (!chart) return;

    new Chart(chart, {

        type: 'doughnut',

        data: {

            labels: ['Completed', 'Pending'],

            datasets: [{
                data: [72, 28],
                backgroundColor: [
                    '#16a34a',
                    '#f59e0b'
                ],
                borderWidth: 0,
                hoverOffset: 8
            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            cutout: '72%',

            plugins: {

                legend: {

                    position: 'bottom',

                    labels: {

                        usePointStyle: true,

                        padding: 20,

                        boxWidth: 10

                    }

                }

            }

        }

    });

});