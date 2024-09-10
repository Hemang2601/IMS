<?php
include 'includes/header.php';
include 'session_check.php';
include 'includes/db.php';

// Fetch all items from the database (assuming you have a table named 'items' for the stock)
$query = "SELECT name, quantity, price, created_at FROM items";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<div class='report-container'>";
    echo "<h1>Stock Report</h1>";
    echo "<p>Here is the current status of your stock:</p>";

    echo "<table class='report-table'>";
    echo "<thead><tr><th>Item Name</th><th>Quantity</th><th>Price per Item</th><th>Date Added</th><th>Time Added</th></tr></thead>";
    echo "<tbody>";

    // Loop through and display each item
    while($row = $result->fetch_assoc()) {
        // Convert the created_at timestamp into separate date and time
        $datetime = new DateTime($row['created_at']);
        $date = $datetime->format('d M Y');  // Date format
        $time = $datetime->format('h:i A');  // Time format

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>₹" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($date) . "</td>";
        echo "<td>" . htmlspecialchars($time) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<p>No items found in stock.</p>";
}

include 'includes/footer.php';
?>
