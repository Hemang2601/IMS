<?php include 'includes/header.php'; ?>
<?php include 'session_check.php'; ?>

<!-- Page Content -->
<div class="dashboard-content">
    <h2>Welcome to the Dashboard</h2>

    <!-- Meters Section -->
    <div class="meters">
        <div class="meter-container">
            <h3>Total Quantity</h3>
            <canvas id="totalQtyMeter"></canvas>
        </div>
        <div class="meter-container">
            <h3>Sales</h3>
            <canvas id="salesMeter"></canvas>
        </div>
        <div class="meter-container">
            <h3>Profit</h3>
            <canvas id="profitMeter"></canvas>
        </div>
        <div class="meter-container">
            <h3>Stock</h3>
            <canvas id="stockMeter"></canvas>
        </div>
        <div class="meter-container">
            <h3>Dead Stock</h3>
            <canvas id="deadStockMeter"></canvas>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Total Quantity Meter (40% filled)
    var ctxTotalQty = document.getElementById('totalQtyMeter').getContext('2d');
    var totalQtyMeter = new Chart(ctxTotalQty, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [40, 60], // 40% filled
                backgroundColor: ['#4CAF50', '#E0E0E0'], // Green fill, light gray background
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Adjusted to make the circle smaller
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });

    // Sales Meter (60% filled)
    var ctxSales = document.getElementById('salesMeter').getContext('2d');
    var salesMeter = new Chart(ctxSales, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [60, 40], // 60% filled
                backgroundColor: ['#FF9800', '#E0E0E0'], // Orange fill, light gray background
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Adjusted to make the circle smaller
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });

    // Profit Meter (50% filled)
    var ctxProfit = document.getElementById('profitMeter').getContext('2d');
    var profitMeter = new Chart(ctxProfit, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [50, 50], // 50% filled
                backgroundColor: ['#FFC107', '#E0E0E0'], // Yellow fill, light gray background
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Adjusted to make the circle smaller
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });

    // Stock Meter (70% filled)
    var ctxStock = document.getElementById('stockMeter').getContext('2d');
    var stockMeter = new Chart(ctxStock, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [70, 30], // 70% filled
                backgroundColor: ['#2196F3', '#E0E0E0'], // Blue fill, light gray background
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Adjusted to make the circle smaller
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });

    // Dead Stock Meter (30% filled)
    var ctxDeadStock = document.getElementById('deadStockMeter').getContext('2d');
    var deadStockMeter = new Chart(ctxDeadStock, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [30, 70], // 30% filled
                backgroundColor: ['#F44336', '#E0E0E0'], // Red fill, light gray background
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Adjusted to make the circle smaller
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {

// Custom Plugin to display the percentage in the center
const centerTextPlugin = {
    id: 'centerText',
    beforeDraw: function(chart) {
        if (chart.config.type === 'doughnut') {
            const width = chart.width;
            const height = chart.height;
            const ctx = chart.ctx;
            ctx.restore();

            const fontSize = (height / 150).toFixed(2);
            ctx.font = `${fontSize}em sans-serif`;
            ctx.textBaseline = 'middle';

            const text = Math.round(chart.data.datasets[0].data[0]) + '%';
            const textX = Math.round((width - ctx.measureText(text).width) / 2);
            const textY = height / 2;

            ctx.fillText(text, textX, textY);
            ctx.save();
        }
    }
};

// Registering the plugin
Chart.register(centerTextPlugin);

// Total Quantity Meter (40% filled)
var ctxTotalQty = document.getElementById('totalQtyMeter').getContext('2d');
var totalQtyMeter = new Chart(ctxTotalQty, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [40, 60], // 40% filled
            backgroundColor: ['#4CAF50', '#E0E0E0'], // Green fill, light gray background
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%', // Adjusted to make the circle smaller
        responsive: true,
        plugins: {
            tooltip: { enabled: false },
            legend: { display: false }
        }
    }
});

// Sales Meter (60% filled)
var ctxSales = document.getElementById('salesMeter').getContext('2d');
var salesMeter = new Chart(ctxSales, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [60, 40], // 60% filled
            backgroundColor: ['#FF9800', '#E0E0E0'], // Orange fill, light gray background
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        plugins: {
            tooltip: { enabled: false },
            legend: { display: false }
        }
    }
});

// Profit Meter (50% filled)
var ctxProfit = document.getElementById('profitMeter').getContext('2d');
var profitMeter = new Chart(ctxProfit, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [50, 50], // 50% filled
            backgroundColor: ['#FFC107', '#E0E0E0'], // Yellow fill, light gray background
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        plugins: {
            tooltip: { enabled: false },
            legend: { display: false }
        }
    }
});

// Stock Meter (70% filled)
var ctxStock = document.getElementById('stockMeter').getContext('2d');
var stockMeter = new Chart(ctxStock, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [70, 30], // 70% filled
            backgroundColor: ['#2196F3', '#E0E0E0'], // Blue fill, light gray background
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        plugins: {
            tooltip: { enabled: false },
            legend: { display: false }
        }
    }
});

// Dead Stock Meter (30% filled)
var ctxDeadStock = document.getElementById('deadStockMeter').getContext('2d');
var deadStockMeter = new Chart(ctxDeadStock, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [30, 70], // 30% filled
            backgroundColor: ['#F44336', '#E0E0E0'], // Red fill, light gray background
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        plugins: {
            tooltip: { enabled: false },
            legend: { display: false }
        }
    }
});
});

</script>

