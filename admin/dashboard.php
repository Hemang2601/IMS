<?php
include 'includes/header.php';
include 'session_check.php';
include 'includes/db.php';

// Fetch the total number of shops
$shop_sql = "SELECT COUNT(DISTINCT store_name) AS total_shops FROM users WHERE role = 0";
$shop_result = $conn->query($shop_sql);
$shop_count = 0;
if ($shop_result && $shop_row = $shop_result->fetch_assoc()) {
    $shop_count = $shop_row['total_shops'];
}

// Fetch the shop names and their categories
$shops_sql = "SELECT DISTINCT store_name, store_category FROM users WHERE role = 0";
$shops_result = $conn->query($shops_sql);
$shops = [];
while ($shop_row = $shops_result->fetch_assoc()) {
    $shops[] = [
        'store_name' => $shop_row['store_name'],
        'store_category' => $shop_row['store_category']
    ];
}

// Fetch the total number of users with role = 0
$user_sql = "SELECT COUNT(*) AS total_users FROM users WHERE role = 0";
$user_result = $conn->query($user_sql);
$user_count = 0;
if ($user_result && $user_row = $user_result->fetch_assoc()) {
    $user_count = $user_row['total_users'];
}
?>

<div class="container">
    <h2>Statistics</h2>
    <div class="stats-container">
        <div class="stat-item">
            <h3>Total Shops</h3>
            <p><?php echo htmlspecialchars($shop_count); ?></p>
        </div>
        <div class="stat-item">
            <h3>Total Users</h3>
            <p><?php echo htmlspecialchars($user_count); ?></p>
        </div>
    </div>
    <div class="shop-table">
        <h3>Shop Names and Categories</h3>
        <table>
            <thead>
                <tr>
                    <th>Shop Name</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shops as $shop): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($shop['store_name']); ?></td>
                        <td><?php echo htmlspecialchars($shop['store_category']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
        font-size: 2em;
        font-weight: bold;
    }

    .stats-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .stat-item {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        flex: 1 1 300px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }

    .stat-item:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .stat-item h3 {
        color: #2196F3;
        font-size: 1.5em;
        margin-bottom: 15px;
    }

    .stat-item p {
        font-size: 2em;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .shop-table {
        margin-top: 30px;
    }

    .shop-table h3 {
        margin-bottom: 20px;
        color: #333;
        font-size: 1.5em;
    }

    .shop-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .shop-table th,
    .shop-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .shop-table th {
        background-color: #2196F3;
        color: white;
        font-size: 1.2em;
    }

    .shop-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .shop-table tr:hover {
        background-color: #ddd;
    }

    @media (max-width: 768px) {
        .stats-container {
            flex-direction: column;
        }

        .stat-item {
            flex: 1 1 100%;
        }

        .shop-table table {
            font-size: 14px;
        }
    }
</style>
