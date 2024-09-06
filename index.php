<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="auth-container">
    <!-- Login Form -->
    <div class="auth-box" id="login-box">
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        <form id="login-form" action="login.php" method="post">
            <div class="form-group">
                <label for="login-email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="login-email" required name="email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="login-password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="login-password" required name="password" placeholder="Enter your password">
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php" class="toggle-link"><i class="fas fa-user-plus"></i> Sign up!</a></p>
    </div>
</div>

</body>
</html>
