<?php
include 'includes/header.php';
include 'session_check.php';
include 'includes/db.php';

// Ensure $userId is defined
$userId = $_SESSION['id']; // Example of how you might get the userId from session

// Fetch user data
$userQuery = "SELECT firstname, lastname, username, email, store_name, store_category, phone_number FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch item data
$itemQuery = "SELECT i.name, i.quantity, i.price, i.image, c.category_name 
              FROM items i 
              JOIN categories c ON i.category_id = c.id 
              WHERE i.user_id = ?";
$stmt = $conn->prepare($itemQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$itemResult = $stmt->get_result();

// Fetch category data
$categoryQuery = "SELECT category_name, category_description FROM categories";
$categoryResult = $conn->query($categoryQuery);

// Summary data
$itemCountQuery = "SELECT COUNT(*) AS total_items, SUM(quantity) AS total_quantity, SUM(price * quantity) AS total_amount FROM items WHERE user_id = ?";
$stmt = $conn->prepare($itemCountQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$itemSummaryResult = $stmt->get_result();
$itemSummary = $itemSummaryResult->fetch_assoc();

$categoryCountQuery = "SELECT COUNT(*) AS total_categories FROM categories";
$categoryCountResult = $conn->query($categoryCountQuery);
$categoryCount = $categoryCountResult->fetch_assoc();

// Calculate profit and loss
$profitLossQuery = "SELECT SUM(price * quantity) AS total_revenue, (SELECT SUM(price * quantity) FROM items WHERE user_id = ?) AS total_cost FROM items WHERE user_id = ?";
$stmt = $conn->prepare($profitLossQuery);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$profitLossResult = $stmt->get_result();
$profitLoss = $profitLossResult->fetch_assoc();
$profitLoss['total_profit'] = $profitLoss['total_revenue'] - $profitLoss['total_cost'];

// Fetch category distribution data
$categoryDistributionQuery = "
    SELECT c.category_name, COUNT(i.id) AS item_count 
    FROM items i 
    JOIN categories c ON i.category_id = c.id 
    WHERE i.user_id = ? 
    GROUP BY c.category_name";
$stmt = $conn->prepare($categoryDistributionQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$categoryDistributionResult = $stmt->get_result();
$categoryDistribution = [];
while ($row = $categoryDistributionResult->fetch_assoc()) {
    $categoryDistribution[] = $row;
}

// Fetch item distribution data
$itemDistributionQuery = "
    SELECT i.name, COUNT(i.id) AS item_count 
    FROM items i 
    WHERE i.user_id = ? 
    GROUP BY i.name";
$stmt = $conn->prepare($itemDistributionQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$itemDistributionResult = $stmt->get_result();
$itemDistribution = [];
while ($row = $itemDistributionResult->fetch_assoc()) {
    $itemDistribution[] = $row;
}

// Fetch monthly stock value data
$monthlyStockValueQuery = "
    SELECT 
        DATE_FORMAT(i.created_at, '%Y-%m') AS month, 
        SUM(i.quantity * i.price) AS total_value 
    FROM items i 
    WHERE i.user_id = ? 
    GROUP BY DATE_FORMAT(i.created_at, '%Y-%m')";
$stmt = $conn->prepare($monthlyStockValueQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$monthlyStockValueResult = $stmt->get_result();
$monthlyStockValueData = [];
while ($row = $monthlyStockValueResult->fetch_assoc()) {
    $monthlyStockValueData[] = $row;
}
?>

<!-- Page Content -->
<div class="dashboard-content">
    <h2>Welcome, <?php echo htmlspecialchars($user['firstname']); ?></h2>

    <!-- User Info Card -->
    <div class="card user-info-card">
        <div class="card-header">
            <i class="fas fa-store"></i>
            <h3>Store Information</h3>
        </div>
        <div class="card-body">
            <p><strong>Store Name:</strong> <?php echo htmlspecialchars($user['store_name']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($user['store_category']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="card summary-card">
            <div class="card-header">
                <i class="fas fa-box"></i>
                <h3>Total Items</h3>
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($itemSummary['total_items']); ?></p>
            </div>
        </div>
        <div class="card summary-card">
            <div class="card-header">
                <i class="fas fa-cube"></i>
                <h3>Total Quantity</h3>
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($itemSummary['total_quantity']); ?></p>
            </div>
        </div>
        <div class="card summary-card">
            <div class="card-header">
                <i class="fas fa-rupee-sign"></i>
                <h3>Total Amount</h3>
            </div>
            <div class="card-body">
                <p>₹<?php echo number_format($itemSummary['total_amount'], 2); ?></p>
            </div>
        </div>
        <div class="card summary-card">
            <div class="card-header">
                <i class="fas fa-tags"></i>
                <h3>Total Categories</h3>
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($categoryCount['total_categories']); ?></p>
            </div>
        </div>
    </div>

    <!-- Profit and Loss Section -->
    <div class="card profit-loss-card">
        <div class="card-header">
            <i class="fas fa-dollar-sign"></i>
            <h3>Profit and Loss</h3>
        </div>
        <div class="card-body">
            <p><strong>Total Revenue:</strong> ₹<?php echo number_format($profitLoss['total_revenue'], 2); ?></p>
            <p><strong>Total Cost:</strong> ₹<?php echo number_format($profitLoss['total_cost'], 2); ?></p>
            <p><strong>Profit:</strong> ₹<?php echo number_format($profitLoss['total_profit'], 2); ?></p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <!-- Category Distribution Chart -->
        <div class="card chart-card">
            <div class="card-header">
                <i class="fas fa-pie-chart"></i>
                <h3>Category Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Prepare data for the pie chart
                    const categoryData = <?php echo json_encode($categoryDistribution); ?>;
                    const labels = categoryData.map(item => item.category_name);
                    const data = categoryData.map(item => item.item_count);

                    // Render pie chart using Chart.js
                    const ctx = document.getElementById('categoryChart').getContext('2d');
                    const categoryChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Category Distribution',
                                data: data,
                                backgroundColor: [
                                    '#FF6384',
                                    '#36A2EB',
                                    '#FFCE56',
                                    '#4BC0C0',
                                    '#9966FF',
                                    '#FF9F40',
                                    '#FF8C00',
                                    '#FFD700',
                                    '#ADFF2F',
                                    '#FF1493'
                                ],
                                borderColor: '#fff',
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
                                            return tooltipItem.label + ': ' + tooltipItem.raw;
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>

        <!-- Item Distribution Chart -->
        <div class="card chart-card">
            <div class="card-header">
                <i class="fas fa-pie-chart"></i>
                <h3>Item Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="itemChart"></canvas>
                <script>
                    // Prepare data for the pie chart
                    const itemData = <?php echo json_encode($itemDistribution); ?>;
                    const itemLabels = itemData.map(item => item.name);
                    const itemValues = itemData.map(item => item.item_count);

                    // Render pie chart using Chart.js
                    const ctx2 = document.getElementById('itemChart').getContext('2d');
                    const itemChart = new Chart(ctx2, {
                        type: 'pie',
                        data: {
                            labels: itemLabels,
                            datasets: [{
                                label: 'Item Distribution',
                                data: itemValues,
                                backgroundColor: [
                                    '#FF6384',
                                    '#36A2EB',
                                    '#FFCE56',
                                    '#4BC0C0',
                                    '#9966FF',
                                    '#FF9F40',
                                    '#FF8C00',
                                    '#FFD700',
                                    '#ADFF2F',
                                    '#FF1493'
                                ],
                                borderColor: '#fff',
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
                                            return tooltipItem.label + ': ' + tooltipItem.raw;
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>

        <!-- Monthly Stock Value Chart -->
        <div class="card chart-card">
            <div class="card-header">
                <i class="fas fa-bar-chart"></i>
                <h3>Monthly Stock Value</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyStockValueChart"></canvas>
                <script>
                    // Prepare data for the bar chart
                    const monthlyStockValueData = <?php echo json_encode($monthlyStockValueData); ?>;
                    const months = monthlyStockValueData.map(item => item.month);
                    const values = monthlyStockValueData.map(item => item.total_value);

                    // Render bar chart using Chart.js
                    const ctx3 = document.getElementById('monthlyStockValueChart').getContext('2d');
                    const monthlyStockValueChart = new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Stock Value (₹)',
                                data: values,
                                backgroundColor: '#36A2EB',
                                borderColor: '#fff',
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
                                            return tooltipItem.label + ': ₹' + tooltipItem.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value, index, values) {
                                            return '₹' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
