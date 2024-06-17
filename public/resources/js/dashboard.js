document.addEventListener("DOMContentLoaded", function() {
    // Acceder a los datos de los atributos data-* del elemento canvas
    const canvas = document.getElementById('myChart');
    const temas = JSON.parse(canvas.getAttribute('data-temas'));
    const cantidadPosts = JSON.parse(canvas.getAttribute('data-cantidad-posts'));

    // Redondear valores en cantidadPosts para asegurar números enteros
    const cantidadPostsEnteros = cantidadPosts.map(value => Math.round(value));

    // Configuración de la gráfica
    const ctx = canvas.getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: temas,
            datasets: [{
                label: 'Total Posts',
                data: cantidadPostsEnteros,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1  // Forzar valores enteros en el eje Y
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: '5 Most Popular Topics'
                },
                legend: {
                    display: false
                }
            }
        }
    });
});