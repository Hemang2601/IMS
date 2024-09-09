<?php 
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

// Handle form submission
$category_name = "";
$category_description = "";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);
    $category_description = trim($_POST['category_description']);
    
    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $query = "SELECT * FROM categories WHERE category_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Category already exists.";
        } else {
            $query = "INSERT INTO categories (category_name, category_description) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $category_name, $category_description);
            if ($stmt->execute()) {
                $success = "Category added successfully.";
            } else {
                $error = "Error adding category. Please try again.";
            }
        }
    }
}

$query = "SELECT * FROM categories";
$categories_result = $conn->query($query);
?>

<div class="category-container">
    <div class="category-header">
        <h2>Manage Categories</h2>
        <button id="addCategoryBtn" class="btn-add-category"><i class="fas fa-plus"></i> Add Category</button>
    </div>

    <table class="category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $categories_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category_description']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Lightbox for Adding Category -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add New Category</h3>

        <form action="" method="POST" class="form-container">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="category_description">Category Description:</label>
                <textarea id="category_description" name="category_description"><?php echo htmlspecialchars($category_description); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- External JS and SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Get the modal
    var modal = document.getElementById("categoryModal");

    // Get the button that opens the modal
    var btn = document.getElementById("addCategoryBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // SweetAlert for success or error messages after form submission
    <?php if (!empty($success)): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo htmlspecialchars($success); ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    <?php elseif (!empty($error)): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo htmlspecialchars($error); ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
