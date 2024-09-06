<?php include 'session_check.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/add_item.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to external JS (if needed) -->
    <script src="assets/js/scripts.js" defer></script>
</head>
<body>

<!-- Navigation Bar -->
<header>
    <nav class="navbar">
        <div class="container">
            <!-- Text-based Logo -->
            <a href="dashboard.php" style="text-decoration: none; color: #ffffff;">
                <h1 style="font-size: 28px; font-weight: 700; margin: 0; letter-spacing: 1px; font-family: 'Roboto', sans-serif;">
                    Inventory Management System
                </h1>
            </a>

            <!-- Navigation Links -->
            <ul class="navbar-links">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                
                <!-- Inventory Dropdown -->
                <li class="dropdown">
                    <button class="dropbtn" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-boxes"></i> Inventory <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" aria-label="Inventory options">
                        <a href="add_item.php"><i class="fas fa-plus"></i> Add Item</a>
                        <a href="update_item.php"><i class="fas fa-edit"></i> Update Item</a>
                        <a href="remove_item.php"><i class="fas fa-trash"></i> Remove Item</a>
                        <a href="view_items.php"><i class="fas fa-eye"></i> View All Items</a>
                    </div>
                </li>

                <li><a href="buy.php"><i class="fas fa-shopping-cart"></i> Buy</a></li>
                <li><a href="sell.php"><i class="fas fa-store"></i> Sell</a></li>
                <li><a href="generate_report.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>

            <!-- User Profile Dropdown -->
            <div class="dropdown navbar-profile">
                <button class="dropbtn profile-btn" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    <span class="username"><?php echo htmlspecialchars($username); ?></span>
                    <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-content profile-dropdown" aria-label="User Profile options">
                    <a href="profile.php"><i class="fas fa-user-cog"></i> My Profile</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Rest of your page content -->

</body>
</html>
