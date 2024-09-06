<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="auth-container">
    <!-- Signup Form -->
    <div class="auth-box" id="signup-box">
        <h2><i class="fas fa-user-plus"></i> Sign Up</h2>
        <form id="signup-form" action="signup_process.php" method="post">
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="signup-firstname"><i class="fas fa-user"></i> First Name</label>
                    <input type="text" id="signup-firstname" required name="firstname" placeholder="Enter your first name">
                </div>
                <div class="form-group half-width">
                    <label for="signup-lastname"><i class="fas fa-user"></i> Last Name</label>
                    <input type="text" id="signup-lastname" required name="lastname" placeholder="Enter your last name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="signup-username"><i class="fas fa-user-tag"></i> Username</label>
                    <input type="text" id="signup-username" required name="username" placeholder="Choose a username">
                </div>
                <div class="form-group half-width">
                    <label for="signup-email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="signup-email" required name="email" placeholder="Enter your email">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="signup-store-name"><i class="fas fa-store"></i> Store Name</label>
                    <input type="text" id="signup-store-name" required name="store_name" placeholder="Enter your store name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="signup-store-category"><i class="fas fa-tags"></i> Store Category</label>
                    <select id="signup-store-category" name="store_category" required>
                        <option value="" disabled selected>Select Store Category</option>
                        <option value="electronics">Electronics Shop</option>
                        <option value="clothing">Clothing Shop</option>
                        <option value="grocery">Grocery Shop</option>
                        <option value="footwear">Footwear Shop</option>
                    </select>
                </div>
                <div class="form-group half-width">
                    <label for="signup-phone-number"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" id="signup-phone-number" required name="phone_number" placeholder="Enter your phone number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="signup-password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="signup-password" required name="password" placeholder="Choose a password">
                </div>
            </div>
            <button type="submit"><i class="fas fa-user-plus"></i> Sign Up</button>
        </form>
        <p>Already have an account? <a href="index.php" class="toggle-link"><i class="fas fa-sign-in-alt"></i> Login</a></p>
    </div>
</div>

</body>
</html>
