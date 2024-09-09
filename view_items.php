<?php 
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php'; 

// Fetch items from the database
$sql = "SELECT * FROM items WHERE user_id = $user_id";
$result = $conn->query($sql);
?>

<div class="items-container">
    <h1 class="table-heading">Your Inventory Items</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['image']): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="item-image-table">
                            <?php else: ?>
                                <img src="default-image.png" alt="No Image" class="item-image-table">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<?php
// Close the database connection
$conn->close();
?>
