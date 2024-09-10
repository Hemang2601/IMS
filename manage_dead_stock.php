<?php 
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sell_full') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Insert into sales table
    $stmt = $conn->prepare("INSERT INTO sales (item_id, quantity, total_amount, sale_type, sale_date) VALUES (?, ?, ?, 'Full Sale', NOW())");
    $total_amount = $quantity * $price;
    $stmt->bind_param("iid", $item_id, $quantity, $total_amount);
    if ($stmt->execute()) {
        // Update item quantity to 0 after selling full
        $stmt_update = $conn->prepare("UPDATE items SET quantity = 0 WHERE id = ?");
        $stmt_update->bind_param("i", $item_id);
        $stmt_update->execute();
        
        $update_success = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'offer') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    // Insert into sales table as an Offer
    $stmt = $conn->prepare("INSERT INTO sales (item_id, quantity, total_amount, sale_type, sale_date) VALUES (?, ?, ?, 'Offer', NOW())");
    $total_amount = $quantity * $price;
    $stmt->bind_param("iid", $item_id, $quantity, $total_amount);
    if ($stmt->execute()) {
        // Update item quantity (if partially sold)
        $stmt_update = $conn->prepare("UPDATE items SET quantity = quantity - ? WHERE id = ?");
        $stmt_update->bind_param("ii", $quantity, $item_id);
        $stmt_update->execute();
        
        $update_success = true;
    }
}

// Prepare and execute the SQL statement for retrieving dead stock items
$stmt = $conn->prepare("SELECT items.*, categories.category_name FROM items 
                        JOIN categories ON items.category_id = categories.id 
                        WHERE items.user_id = ? AND items.quantity != 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total dead stock amount
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

<!-- Lightbox Modal -->
<div id="lightboxModal" class="lightbox-modal">
    <div class="lightbox-content">
        <span class="close-btn">&times;</span>
        <h2 id="lightboxTitle">Action</h2>
        <form id="lightboxForm" method="POST" action="">
            <input type="hidden" name="item_id" id="item_id">
            <input type="hidden" name="quantity" id="quantity">
            <input type="hidden" name="price" id="price">
            <p id="lightboxDescription"></p>
            <button type="submit" class="btn btn-primary">Confirm</button>
        </form>
    </div>
</div>


<?php include 'includes/footer.php'; ?>

<script>
    const lightboxModal = document.getElementById('lightboxModal');
const closeBtn = document.querySelector('.close-btn');

function openLightbox(action, itemId, quantity, price) {
    lightboxModal.style.display = 'flex';  // Changed from 'block' to 'flex' for centering
    document.getElementById('item_id').value = itemId;
    document.getElementById('quantity').value = quantity;
    document.getElementById('price').value = price;

    if (action === 'sell_full') {
        document.getElementById('lightboxTitle').innerText = 'Sell Full Item';
        document.getElementById('lightboxDescription').innerText = 'Are you sure you want to sell this item completely?';
    } else if (action === 'offer') {
        document.getElementById('lightboxTitle').innerText = 'Offer Item';
        document.getElementById('lightboxDescription').innerText = 'Are you sure you want to offer this item?';
    }
    
    document.getElementById('lightboxForm').innerHTML += '<input type="hidden" name="action" value="' + action + '">';
}

closeBtn.onclick = function () {
    lightboxModal.style.display = 'none';
};

window.onclick = function (event) {
    if (event.target === lightboxModal) {
        lightboxModal.style.display = 'none';
    }
};

</script>
