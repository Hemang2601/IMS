<?php
include 'includes/header.php'; 
include 'includes/db.php'; // Include database connection

// Check if the session has already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION["id"]; // Assuming user ID is stored in session

// Fetch categories from the database
$categories = [];
$query = "SELECT id, category_name FROM categories";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize input data
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $item_category_id = (int)$_POST['item_category']; // Category ID
    $item_quantity = (int)$_POST['item_quantity'];
    $item_price = (float)$_POST['item_price'];

    // Handle file upload
    $upload_dir = 'uploads/'; // Directory to save uploaded files

    // Check if the upload directory exists, if not create it
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("Failed to create upload directory.");
        }
    }

    $image_path = null;
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['item_image']['tmp_name'];
        $file_name = basename($_FILES['item_image']['name']);
        $image_path = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file_tmp, $image_path)) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Error!',
                            text: 'Failed to upload image.',
                        });
                    });
                  </script>";
            $image_path = null; // Reset image path in case of error
        }
    }

    // Validate form inputs
    if (!empty($item_name) && $item_category_id > 0 && $item_quantity > 0 && $item_price > 0) {
        // Check if the item already exists in the database
        $check_sql = "SELECT COUNT(*) FROM items WHERE name = '$item_name' AND user_id = $user_id";
        $result = $conn->query($check_sql);
        $row = $result->fetch_array();
        
        if ($row[0] > 0) {
            // Item already exists
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Item Exists!',
                            text: 'An item with this name already exists.',
                        });
                    });
                  </script>";
        } else {
            // Prepare an SQL query to insert the data
            $sql = "INSERT INTO items (name, category_id, quantity, price, user_id, image) 
                    VALUES ('$item_name', $item_category_id, '$item_quantity', '$item_price', $user_id, '$image_path')";

            if ($conn->query($sql) === TRUE) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Item added successfully!',
                            }).then(function() {
                                window.location.href = 'add_item.php'; // Redirect to add item page
                            });
                        });
                      </script>";
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Error: " . $conn->error . "',
                            });
                        });
                      </script>";
            }
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please fill in all fields with valid data.',
                    });
                });
              </script>";
    }
}

// Close database connection
$conn->close();
?>

<div class="addItemContainer">
    <div class="card">
        <h1 class="card-title">Add New Inventory Item</h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" class="add-item-form">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <div class="input-icon">
                    <i class="fas fa-box"></i>
                    <input type="text" id="item_name" name="item_name" required placeholder="Enter item name">
                </div>
            </div>

            <div class="form-group">
                <label for="item_category">Category</label>
                <div class="input-icon select-wrapper">
                    <i class="fas fa-tags"></i>
                    <select id="item_category" name="item_category" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

            <div class="form-group">
                <label for="item_image">Image</label>
                <div class="input-icon">
                    <i class="fas fa-image"></i>
                    <input type="file" id="item_image" name="item_image" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Add Item</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
