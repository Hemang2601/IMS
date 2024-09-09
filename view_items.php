<?php 
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php'; 

// Fetch categories from the database
$category_sql = "SELECT * FROM categories";
$categories_result = $conn->query($category_sql);

// Fetch items from the database
$sql = "SELECT items.*, categories.category_name FROM items 
        JOIN categories ON items.category_id = categories.id 
        WHERE items.user_id = $user_id";
$result = $conn->query($sql);
?>

<div class="items-container">
    <h1 class="table-heading">Your Inventory Items</h1>

    <?php if ($categories_result->num_rows > 0): ?>
        <?php while ($category = $categories_result->fetch_assoc()): ?>
            <h2 class="category-heading"><?php echo htmlspecialchars($category['category_name']); ?></h2>

            <?php 
            // Fetch items for the current category
            $category_id = $category['id'];
            $items_sql = "SELECT * FROM items WHERE user_id = $user_id AND category_id = $category_id";
            $items_result = $conn->query($items_sql);
            ?>

            <?php if ($items_result->num_rows > 0): ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $items_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="item-image-table">
                                    <?php else: ?>
                                        <img src="default-image.png" alt="No Image" class="item-image-table">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No items found in this category.</p>
            <?php endif; ?>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

<?php
// Close the database connection
$conn->close();
?>
