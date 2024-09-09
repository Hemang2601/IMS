<?php 
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

// Initialize success flags
$delete_success = false;
$update_success = false;

// Handle item deletion
if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);

    // Ensure the item belongs to the current user
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);

    if ($stmt->execute()) {
        $delete_success = true;
    } else {
        echo "Error deleting item: " . $conn->error;
    }
    $stmt->close();
}

// Handle item update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_item') {
    $item_id = intval($_POST['item_id']);
    $name = htmlspecialchars($_POST['name']);
    $category_id = intval($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];

    // Fetch the current item details
    $stmt = $conn->prepare("SELECT image FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

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
    $stmt = $conn->prepare("UPDATE items SET name = ?, category_id = ?, quantity = ?, price = ?, image = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("siidisii", $name, $category_id, $quantity, $price, $image, $item_id, $user_id);

    if ($stmt->execute()) {
        $update_success = true;
    } else {
        echo "Error updating item: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all items with categories from the database
$stmt = $conn->prepare("SELECT items.*, categories.category_name FROM items 
                        JOIN categories ON items.category_id = categories.id 
                        WHERE items.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

    <!-- Category Dropdown -->
    <div class="category-dropdown">
        <select id="categorySelect" onchange="filterCategory(this.value)">
            <option value="all">All Categories</option>
            <?php
            // Fetch unique categories for the dropdown
            $categories = $conn->query("SELECT * FROM categories");
            while ($cat = $categories->fetch_assoc()) {
                echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
            }
            ?>
        </select>
    </div>

    <!-- Items Table -->
<table class="items-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="itemsTableBody">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr data-category="' . htmlspecialchars($row['category_id']) . '">
                    <td><img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" class="item-image"></td>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . htmlspecialchars($row['category_name']) . '</td>
                    <td>' . htmlspecialchars($row['quantity']) . '</td>
                    <td>₹' . htmlspecialchars($row['price']) . '</td>
                    <td class="item-card-actions">
                        <a href="javascript:void(0);" class="btn-edit" onclick="openEditModal(' . $row['id'] . ', \'' . addslashes($row['name']) . '\', ' . $row['category_id'] . ', ' . $row['quantity'] . ', ' . $row['price'] . ')">
                            <button class="btn btn-icon btn-primary"><i class="fas fa-edit"></i></button>
                        </a>
                        <a href="manage_items.php?delete=' . $row['id'] . '" class="btn-delete">
                            <button class="btn btn-icon btn-danger"><i class="fas fa-trash"></i></button>
                        </a>
                    </td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="6">No items found.</td></tr>';
        }
        ?>
    </tbody>
</table>

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
            <select id="item_category" name="category" required>
                <?php
                $categories = $conn->query("SELECT * FROM categories");
                while ($cat = $categories->fetch_assoc()) {
                    echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                }
                ?>
            </select>
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

function filterCategory(categoryId) {
    var tableRows = document.getElementById('itemsTableBody').getElementsByTagName('tr');
    for (var i = 0; i < tableRows.length; i++) {
        var row = tableRows[i];
        if (row.getAttribute('data-category') === categoryId || categoryId === 'all') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}
</script>
<?php include 'includes/footer.php'; ?>
