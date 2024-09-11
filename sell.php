<?php
include 'includes/header.php'; 
include 'includes/db.php'; 

// Initialize variables
$item_id = $quantity = $price = $sale_name = $customer_phone = "";
$sale_message = "";

// Fetch items from the database
$items = [];
$items_query = "SELECT id, name, price FROM items";
$items_result = $conn->query($items_query);

if ($items_result->num_rows > 0) {
    while($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $sale_name = $_POST['sale_name'];
    $customer_phone = $_POST['customer_phone'];

    // Basic validation
    if (empty($item_id) || empty($quantity) || empty($price) || empty($sale_name) || empty($customer_phone)) {
        $sale_message = "<div class='alert danger'>All fields are required.</div>";
    } elseif (!is_numeric($quantity) || !is_numeric($price)) {
        $sale_message = "<div class='alert danger'>Quantity and Price must be numeric.</div>";
    } else {
        // Calculate total amount
        $total_amount = $quantity * $price;

        // Fetch item name
        $item_query = $conn->prepare("SELECT name FROM items WHERE id = ?");
        $item_query->bind_param("i", $item_id);
        $item_query->execute();
        $item_result = $item_query->get_result();
        $item_name = $item_result->fetch_assoc()['name'];
        $item_query->close();

        // Insert sale data into sales table
        $insert_sale = $conn->prepare("INSERT INTO sales (item_id, quantity, price_per_unit, total_amount, sale_name, customer_phone) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_sale->bind_param("iiddss", $item_id, $quantity, $price, $total_amount, $sale_name, $customer_phone);
        if ($insert_sale->execute()) {
            $sale_message = "<div class='alert success'>Sale recorded successfully.</div>";
        } else {
            $sale_message = "<div class='alert danger'>Error recording sale: " . $conn->error . "</div>";
        }
        $insert_sale->close();

        // Show sale details
        $sale_message .= "
            <div class='invoice'>
                <h3>Invoice</h3>
                <div class='row'>
                    <div class='col'>
                        <h5>Customer Details</h5>
                        <p><strong>Name:</strong> $sale_name</p>
                        <p><strong>Phone:</strong> $customer_phone</p>
                    </div>
                    <div class='col text-right'>
                        <h5>Invoice Date</h5>
                        <p>" . date('Y-m-d H:i:s') . "</p>
                    </div>
                </div>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price per Unit</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$item_name</td>
                            <td>$quantity</td>
                            <td>$price</td>
                            <td>$total_amount</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan='3'>Total Quantity</th>
                            <th>$quantity</th>
                        </tr>
                        <tr>
                            <th colspan='3'>Total Amount</th>
                            <th>$total_amount</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        ";
    }
}

$conn->close();
?>

<div class="sellcontainer">
    <h2>Record a Sale</h2>

    <!-- Sale Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form">
        <div class="form-group">
            <label for="sale_name">Customer Name:</label>
            <input type="text" id="sale_name" name="sale_name" value="<?php echo htmlspecialchars($sale_name); ?>" required>
        </div>
        <div class="form-group">
            <label for="customer_phone">Customer Phone:</label>
            <input type="text" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($customer_phone); ?>" required>
        </div>
        <div class="form-group">
            <label for="item_id">Item:</label>
            <select id="item_id" name="item_id" required>
                <option value="">Select an item</option>
                <?php foreach ($items as $item): ?>
                <option value="<?php echo htmlspecialchars($item['id']); ?>"><?php echo htmlspecialchars($item['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" step="1" min="1" required>
        </div>
        <div class="form-group">
            <label for="price">Price per Unit:</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" step="0.01" min="0" required>
        </div>
        <button type="submit" class="btn">Record Sale</button>
    </form>

    <!-- Sale Message -->
    <?php if ($sale_message): ?>
    <div class="sale-message">
        <?php echo $sale_message; ?>
    </div>
    <?php endif; ?>

    <!-- Current Items -->
    <h2>Available Items</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id']); ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['price']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
