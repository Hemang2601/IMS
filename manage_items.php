<?php 
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

// Handle item deletion
$delete_success = false;
if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);

    // Ensure the item belongs to the current user
    $sql = "DELETE FROM items WHERE id = $item_id AND user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $delete_success = true;
    } else {
        echo "Error deleting item: " . $conn->error;
    }
}

// Handle item update
$update_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_item') {
    $item_id = intval($_POST['item_id']);
    $name = htmlspecialchars($_POST['name']);
    $category = htmlspecialchars($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];

    // Fetch the current item details
    $sql = "SELECT image FROM items WHERE id = $item_id AND user_id = $user_id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();

    // Update item image if a new file is uploaded
    if (!empty($image)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

        // Check if image file is an actual image
        $check = getimagesize($image_temp);
        if ($check === false) {
            echo "File is not an image.";
            exit;
        }

        // Upload image
        if (!move_uploaded_file($image_temp, $target_file)) {
            echo "Error uploading image.";
            exit;
        }

        $image = $target_file;
    } else {
        $image = $item['image']; // Keep the old image if no new one is uploaded
    }

    // Update item in the database
    $sql_update = "UPDATE items SET name = '$name', category = '$category', quantity = $quantity, price = $price, image = '$image' WHERE id = $item_id AND user_id = $user_id";

    if ($conn->query($sql_update) === TRUE) {
        $update_success = true;
    } else {
        echo "Error updating item: " . $conn->error;
    }
}

// Fetch all items from the database
$sql = "SELECT * FROM items WHERE user_id = $user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <link rel="stylesheet" href="styles/manage_items.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php if ($delete_success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Item deleted successfully!',
            confirmButtonText: 'OK',
            onClose: () => {
                window.location.href = 'manage_items.php'; // Reload the page after deletion
            }
        });
    </script>
<?php endif; ?>

<?php if ($update_success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Item updated successfully!',
            confirmButtonText: 'OK',
            onClose: () => {
                window.location.href = 'manage_items.php'; // Reload the page after update
            }
        });
    </script>
<?php endif; ?>

<div class="items-management-container">
    <h1>Manage Your Items</h1>

    <div class="item-cards-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item-card">
                    <div class="item-card-image">
                        <?php if ($row['image']): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php else: ?>
                            <img src="default-image.png" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="item-card-body">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($row['price']); ?></p>
                        <div class="item-card-actions">
                            <a href="javascript:void(0);" class="btn-edit" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['category']); ?>', <?php echo $row['quantity']; ?>, <?php echo $row['price']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="manage_items.php?delete=<?php echo $row['id']; ?>" class="btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="editItemModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Item</h2>
        <form id="editItemForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="item_id" id="edit_item_id">
            <input type="hidden" name="action" value="update_item">
            <label for="name">Name:</label>
            <input type="text" id="item_name" name="name" required>
            <label for="category">Category:</label>
            <input type="text" id="item_category" name="category" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="item_quantity" name="quantity" required>
            <label for="price">Price:</label>
            <input type="number" id="item_price" name="price" step="0.01" required>
            <label for="image">Image:</label>
            <input type="file" id="item_image" name="image">
            <button type="submit">Update Item</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, category, quantity, price) {
    document.getElementById('edit_item_id').value = id;
    document.getElementById('item_name').value = name;
    document.getElementById('item_category').value = category;
    document.getElementById('item_quantity').value = quantity;
    document.getElementById('item_price').value = price;
    document.getElementById('editItemModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editItemModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == document.getElementById('editItemModal')) {
        closeEditModal();
    }
}
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

<?php include 'includes/footer.php'; ?>
