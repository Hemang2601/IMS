<?php
include 'session_check.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieve the username from session or set default
$username = $_SESSION['firstname'] ?? 'Admin';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management System - Admin</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="css/style.css">  
    <link rel="stylesheet" href="css/manage_users.css">
    <link rel="stylesheet" href="css/profile.css">


    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to external JS (if needed) -->
    <script src="assets/js/scripts.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Navigation Bar -->
<header>
    <nav class="navbar">
        <div class="container">
            <!-- Text-based Logo -->
            <a href="admin/dashboard.php" style="text-decoration: none; color: #ffffff;">
                <h1 style="font-size: 28px; font-weight: 700; margin: 0; letter-spacing: 1px; font-family: 'Roboto', sans-serif;">
                    I M S
                </h1>
            </a>

            <!-- Navigation Links -->
            <ul class="navbar-links">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>

                <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>

                <li> <a href="profile.php"><i class="fas fa-user-cog"></i> My Profile</a></li>
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
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>
</header>
