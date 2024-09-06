<?php include 'includes/header.php'; ?>

<div class="addItemContainer">
    <div class="card">
        <h1 class="card-title">Add New Inventory Item</h1>

        <form action="process_add_item.php" method="POST" class="add-item-form">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <div class="input-icon">
                    <i class="fas fa-box"></i>
                    <input type="text" id="item_name" name="item_name" required placeholder="Enter item name">
                </div>
            </div>

            <div class="form-group">
                <label for="item_category">Category</label>
                <div class="input-icon">
                    <i class="fas fa-tags"></i>
                    <input type="text" id="item_category" name="item_category" required placeholder="Enter category">
                </div>
            </div>

            <div class="form-group">
                <label for="item_quantity">Quantity</label>
                <div class="input-icon">
                    <i class="fas fa-sort-amount-up"></i>
                    <input type="number" id="item_quantity" name="item_quantity" required placeholder="Enter quantity">
                </div>
            </div>

            <div class="form-group">
                <label for="item_price">Price (₹)</label>
                <div class="input-icon">
                    <i class="fas fa-rupee-sign"></i>
                    <input type="number" id="item_price" name="item_price" required placeholder="Enter price">
                </div>
            </div>

            <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Add Item</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


