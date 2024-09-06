<?php
// Check if the session has already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'includes/db.php';

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Initialize variable for user details
$user_id = $_SESSION["id"]; // Assuming user ID is stored in session
$username = $email = $store_name = $store_category = $phone_number = "";

// Fetch user details from the database
$sql = "SELECT username, email, store_name, store_category, phone_number FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id); // Bind the user ID
    $stmt->execute();
    $stmt->bind_result($username, $email, $store_name, $store_category, $phone_number);
    $stmt->fetch();
    $stmt->close();
}

// Close database connection
$conn->close();
?>
