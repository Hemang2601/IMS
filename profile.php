<?php
include 'includes/header.php';
include 'session_check.php';
include 'includes/db.php';

// Fetch user data from the database based on session user_id
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, store_name, store_category, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $store_name = $_POST['store_name'];
    $store_category = $_POST['store_category'];
    $phone_number = $_POST['phone_number'];

    // Prepare the update query
    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, store_name = ?, store_category = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $store_name, $store_category, $phone_number, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile has been successfully updated!',
                confirmButtonColor: '#1e3a8a'
            });
        </script>";
        // Update $user data after successful update
        $user['firstname'] = $firstname;
        $user['lastname'] = $lastname;
        $user['email'] = $email;
        $user['store_name'] = $store_name;
        $user['store_category'] = $store_category;
        $user['phone_number'] = $phone_number;
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update your profile. Please try again later.',
                confirmButtonColor: '#1e3a8a'
            });
        </script>";
    }
}
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Profile</h1>
        <p>Manage your personal information and store details.</p>
    </div>

    <div class="row profile-info">
        <div class="col-md-4">
            <div class="profile-card">
                <img src="https://via.placeholder.com/150" alt="Profile Image" class="profile-image">
                <h3><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></h3>
                <p>@<?php echo htmlspecialchars($user['username']); ?></p>
            </div>
        </div>

        <div class="col-md-8">
    <form action="" method="POST">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="phone_number">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="store_name">Store Name</label>
                <input type="text" class="form-control" id="store_name" name="store_name" value="<?php echo htmlspecialchars($user['store_name']); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="store_category">Store Category</label>
                <input type="text" class="form-control" id="store_category" name="store_category" value="<?php echo htmlspecialchars($user['store_category']); ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
