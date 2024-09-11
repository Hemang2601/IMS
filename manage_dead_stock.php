<?php 
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

$update_success = false;

// Handle 'sell_full' and 'offer' actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $action = $_POST['action'];

    if ($action === 'sell_full') {
        // Full sale: Insert into sales table and update item quantity to 0
        $stmt = $conn->prepare("INSERT INTO sales (item_id, quantity, total_amount, sale_type, sale_date) VALUES (?, ?, ?, 'Full Sale', NOW())");
        $total_amount = $quantity * $price;
        $stmt->bind_param("iid", $item_id, $quantity, $total_amount);

        if ($stmt->execute()) {
            $stmt_update = $conn->prepare("UPDATE items SET quantity = 0 WHERE id = ?");
            $stmt_update->bind_param("i", $item_id);
            $stmt_update->execute();
            $update_success = true;
        }
    } elseif ($action === 'offer') {
        // Offer sale: Insert into sales table and update item quantity
        $stmt = $conn->prepare("INSERT INTO sales (item_id, quantity, total_amount, sale_type, sale_date) VALUES (?, ?, ?, 'Offer', NOW())");
        $total_amount = $quantity * $price;
        $stmt->bind_param("iid", $item_id, $quantity, $total_amount);

        if ($stmt->execute()) {
            $stmt_update = $conn->prepare("UPDATE items SET quantity = quantity - ? WHERE id = ?");
            $stmt_update->bind_param("ii", $quantity, $item_id);
            $stmt_update->execute();
            $update_success = true;
        }
    }
}

// Fetch dead stock items
$stmt = $conn->prepare("SELECT items.*, categories.category_name FROM items 
                        JOIN categories ON items.category_id = categories.id 
                        WHERE items.user_id = ? AND items.quantity != 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total dead stock value
$total_dead_stock_amount = 0;
while ($row = $result->fetch_assoc()) {
    $item_total_value = $row['quantity'] * $row['price'];
    $total_dead_stock_amount += $item_total_value; 
}
?>

<?php if ($update_success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Transaction completed successfully!',
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
                $result->data_seek(0); // Reset result pointer
                
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
                                <button class="btn btn-success" onclick="openLightbox(\'sell_full\', ' . $row['id'] . ', ' . $row['quantity'] . ', ' . $row['price'] . ')"><i class="fas fa-check"></i> Sell Full</button>
                                <button class="btn btn-warning" onclick="openLightbox(\'offer\', ' . $row['id'] . ', ' . $row['quantity'] . ', ' . $row['price'] . ')"><i class="fas fa-tag"></i> Offer</button>
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

<!-- Manage Dead Stock Modal Form -->
<div id="manageDeadStockModal" class="lightbox-modal">
    <div class="lightbox-content">
        <span class="close-btn">&times;</span>
        <h2 id="lightboxTitle">Action</h2>
        
        <!-- Best Practice Form -->
        <form id="lightboxForm" method="POST" action="">
            <!-- Hidden inputs for storing values -->
            <input type="hidden" name="item_id" id="item_id">
            <input type="hidden" name="quantity" id="quantity">
            <input type="hidden" name="price" id="price">
            <input type="hidden" name="action" id="action">
            
            <!-- Action description -->
            <p id="lightboxDescription"></p>
            
            <!-- Quantity Field -->
            <div class="form-group">
                <label for="quantityInput">Enter Quantity:</label>
                <input type="number" name="quantityInput" id="quantityInput" class="form-control" min="1" required>
            </div>
            
            <!-- Total Price Display -->
            <div class="form-group">
                <label for="totalPriceDisplay">Total Price:</label>
                <input type="text" id="totalPriceDisplay" class="form-control" readonly>
            </div>

            <!-- Confirmation Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Confirm</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLightbox(action, itemId, quantity, price) {
        var lightboxModal = document.getElementById('manageDeadStockModal');
        lightboxModal.style.display = 'flex'; 
        document.getElementById('item_id').value = itemId;
        document.getElementById('quantity').value = quantity;
        document.getElementById('price').value = price;
        document.getElementById('action').value = action;

        if (action === 'sell_full') {
            document.getElementById('lightboxTitle').innerText = 'Sell Full Item';
            document.getElementById('lightboxDescription').innerText = 'Are you sure you want to sell this item completely?';
        } else if (action === 'offer') {
            document.getElementById('lightboxTitle').innerText = 'Offer Item';
            document.getElementById('lightboxDescription').innerText = 'Are you sure you want to offer this item?';
        }
    }

    // Update total price based on quantity input
    document.getElementById('quantityInput').addEventListener('input', function () {
        var price = parseFloat(document.getElementById('price').value);
        var quantity = parseInt(this.value);
        var totalPrice = price * quantity;
        document.getElementById('totalPriceDisplay').value = '₹' + totalPrice.toFixed(2);
    });

    // Close modal logic
    var closeBtn = document.querySelector('.close-btn');
    closeBtn.onclick = function () {
        document.getElementById('manageDeadStockModal').style.display = 'none';
    };

    window.onclick = function (event) {
        if (event.target === document.getElementById('manageDeadStockModal')) {
            document.getElementById('manageDeadStockModal').style.display = 'none';
        }
    };
</script>
<?php include 'includes/footer.php'; ?>