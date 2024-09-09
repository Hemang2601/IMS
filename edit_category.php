<?php
// Include necessary files
include 'includes/header.php'; 
include 'session_check.php'; 
include 'includes/db.php';

$category_name = "";
$category_description = "";
$error = "";
$success = "";

// Handle form submission for editing a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "edit") {
    $id = $_POST['id'];
    $category_name = trim($_POST['category_name']);
    $category_description = trim($_POST['category_description']);
    
    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $query = "UPDATE categories SET category_name = ?, category_description = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $category_name, $category_description, $id);
        
        if ($stmt->execute()) {
            $success = "Category updated successfully.";
        } else {
            $error = "Error updating category. Please try again.";
        }
    }
}

$query = "SELECT * FROM categories";
$categories_result = $conn->query($query);
?>

<div class="category-container">
    <div class="category-header">
        <h2>Manage Categories</h2>
        <button id="addCategoryBtn" class="btn-add-category" style="background: #1e3a8a; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; display: none;"><i class="fas fa-plus"></i> Add Category</button>
    </div>

    <table class="category-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $categories_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category_description']); ?></td>
                <td>
                    <button class="edit-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['category_name']); ?>" data-description="<?php echo htmlspecialchars($row['category_description']); ?>" style="background: #1e3a8a; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Edit</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Lightbox for Editing Category -->
<div id="categoryModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; overflow: auto;">
    <div class="modal-content" style="background: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); position: relative;">
        <span class="close" style="position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer; color: #333;">&times;</span>
        <h3 id="modalTitle">Edit Category</h3>

        <?php if (!empty($error)): ?>
            <div class="error-message" style="background: #fdd; color: #d33; padding: 10px; border: 1px solid #d33; border-radius: 4px; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success-message" style="background: #dfd; color: #2d7d2e; padding: 10px; border: 1px solid #2d7d2e; border-radius: 4px; margin-bottom: 15px;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form id="categoryForm" action="" method="POST" class="form-container">
            <input type="hidden" id="category_id" name="id">
            <input type="hidden" id="formAction" name="action" value="edit">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" value="" required style="width: 97%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="category_description">Category Description:</label>
                <textarea id="category_description" name="category_description" style="width: 97%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit" style="background: #1e3a8a; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;"><i class="fas fa-save"></i> <span id="submitBtnText">Update Category</span></button>
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

    // When the user clicks on the button, open the modal (Button to add category is hidden)
    btn.onclick = function() {
        // No action needed here since the button is hidden
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

    // Edit button click event
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            var categoryId = this.getAttribute('data-id');
            var categoryName = this.getAttribute('data-name');
            var categoryDescription = this.getAttribute('data-description');

            document.getElementById('category_id').value = categoryId;
            document.getElementById('category_name').value = categoryName;
            document.getElementById('category_description').value = categoryDescription;
            document.getElementById('formAction').value = "edit";
            document.getElementById('submitBtnText').innerText = "Update Category";
            document.getElementById('modalTitle').innerText = "Edit Category";
            
            modal.style.display = "block";
        });
    });

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
