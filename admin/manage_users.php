<?php
include 'includes/header.php';
include 'session_check.php';
include 'includes/db.php';

// Handle the form submission for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Get form data
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $store_name = $_POST['store_name'];
    $store_category = $_POST['store_category'];
    $phone_number = $_POST['phone_number'];

    // Prepare and execute the SQL update statement
    $sql = "UPDATE users SET firstname=?, lastname=?, username=?, email=?, store_name=?, store_category=?, phone_number=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $firstname, $lastname, $username, $email, $store_name, $store_category, $phone_number, $id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'User updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Error updating user: " . $conn->error . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }

    $stmt->close();
}

// Handle the deletion of a user
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Prepare and execute the SQL delete statement
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'User deleted successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_users.php';
                }
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Error deleting user: " . $conn->error . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_users.php';
                }
            });
        </script>";
    }

    $stmt->close();
}

// Prepare SQL to fetch all users where role is 0
$sql = "SELECT id, firstname, lastname, username, email, store_name, store_category, phone_number FROM users WHERE role = 0";
$result = $conn->query($sql);
?>

<!-- Main Container -->
<div id="manage_user_container">
    <h2>User List</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Store Name</th>
                <th>Store Category</th>
                <th>Phone Number</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Check if there are any users
        if ($result->num_rows > 0) {
            // Loop through each user and display their data
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['store_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['store_category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                echo "<td><button class='edit-btn' data-id='{$row['id']}' data-firstname='{$row['firstname']}' data-lastname='{$row['lastname']}' data-username='{$row['username']}' data-email='{$row['email']}' data-store='{$row['store_name']}' data-category='{$row['store_category']}' data-phone='{$row['phone_number']}'>Edit</button></td>";
                echo "<td><a href='manage_users.php?delete_id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No users found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Lightbox for Editing User -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <form id="editForm" method="POST" action="">
        <input type="hidden" name="id" id="userId">
        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" id="firstname">
        
        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" id="lastname">
        
        <label for="username">Username:</label>
        <input type="text" name="username" id="username">
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email">
        
        <label for="store_name">Store Name:</label>
        <input type="text" name="store_name" id="store_name">
        
        <label for="store_category">Store Category:</label>
        <input type="text" name="store_category" id="store_category">
        
        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" id="phone_number">
        
        <button type="submit" name="update">Save Changes</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Get the modal and close button
    var editModal = document.getElementById("editModal");
    var closeBtn = document.getElementsByClassName("close")[0];

    // Function to open the modal
    function openModal() {
        editModal.style.display = "block";
    }

    // Function to close the modal
    function closeModal() {
        editModal.style.display = "none";
    }

    // Close modal when clicking the 'x'
    closeBtn.onclick = function() {
        closeModal();
    }

    // Close modal when clicking outside the modal
    window.onclick = function(event) {
        if (event.target == editModal) {
            closeModal();
        }
    }

    // Edit button event listener
    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            // Get user data from the data attributes
            var userId = this.getAttribute('data-id');
            var firstname = this.getAttribute('data-firstname');
            var lastname = this.getAttribute('data-lastname');
            var username = this.getAttribute('data-username');
            var email = this.getAttribute('data-email');
            var storeName = this.getAttribute('data-store');
            var storeCategory = this.getAttribute('data-category');
            var phoneNumber = this.getAttribute('data-phone');

            // Populate the form with the user data
            document.getElementById('userId').value = userId;
            document.getElementById('firstname').value = firstname;
            document.getElementById('lastname').value = lastname;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;
            document.getElementById('store_name').value = storeName;
            document.getElementById('store_category').value = storeCategory;
            document.getElementById('phone_number').value = phoneNumber;

            // Open the modal
            openModal();
        });
    });
</script>
