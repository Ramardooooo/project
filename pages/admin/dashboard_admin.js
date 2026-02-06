function toggleTraffic(){
    const body = document.getElementById('trafficBody');
    const icon = document.getElementById('trafficIcon');

    if(body.style.display === 'none'){
        body.style.display = 'block';
        icon.classList.replace('fa-plus','fa-minus');
    } else {
        body.style.display = 'none';
        icon.classList.replace('fa-minus','fa-plus');
    }
}

new Chart(document.getElementById('trafficChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
            data: <?= json_encode($traffic) ?>,
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
