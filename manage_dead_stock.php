<?php 
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_item') {
    // Update logic here
}

// Prepare and execute the SQL statement
$stmt = $conn->prepare("SELECT items.*, categories.category_name FROM items 
                        JOIN categories ON items.category_id = categories.id 
                        WHERE items.user_id = ? AND items.quantity != 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize total dead stock amount variable
$total_dead_stock_amount = 0;

// Iterate over the results and calculate the total dead stock amount
while ($row = $result->fetch_assoc()) {
    $item_total_value = $row['quantity'] * $row['price'];
    $total_dead_stock_amount += $item_total_value; 
}
?>

<?php if ($update_success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Dead stock item updated successfully!',
            confirmButtonText: 'OK',
            onClose: () => {
                window.location.href = 'manage_dead_stock.php'; 
            }
        });
    </script>
<?php endif; ?>

<div class="dead-stock-item-management-container">
    <div class="dashboard-header">
        <h1>Manage Dead Stock</h1>
        <div class="total-amount-display">
            <h2>Total Dead Stock Amount: ₹<?php echo number_format($total_dead_stock_amount, 2); ?></h2>
        </div>
    </div>

    <div class="dead-stock-items-panel">
        <h2>Dead Stock Items</h2>
        <table class="dead-stock-items-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Total Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="deadStockItemsTableBody">
                <?php
                // Reset the result pointer
                $result->data_seek(0);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item_total_value = $row['quantity'] * $row['price'];
                        echo '<tr>
                            <td><img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" class="item-image"></td>
                            <td>' . htmlspecialchars($row['name']) . '</td>
                            <td>' . htmlspecialchars($row['category_name']) . '</td>
                            <td>' . htmlspecialchars($row['quantity']) . '</td>
                            <td>₹' . number_format($item_total_value, 2) . '</td>
                            <td class="item-actions">
                                <a href="sell_full.php?item_id=' . $row['id'] . '" class="btn btn-success"><i class="fas fa-check"></i> Sell Full</a>
                                <a href="offer.php?item_id=' . $row['id'] . '" class="btn btn-warning"><i class="fas fa-tag"></i> Offer</a>
                            </td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">No dead stock items found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<?php
$conn->close();
?>
