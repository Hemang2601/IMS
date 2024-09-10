<?php
// Include database connection
include 'includes/db.php';

// Initialize variables
$email = $password = "";
$email_err = $password_err = "";

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before querying the database
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, firstname, email, password, role FROM users WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = $email;

            // Execute the statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $firstname, $email, $hashed_password, $role);

                    if ($stmt->fetch()) {
                        // Verify password
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["firstname"] = $firstname;
                            $_SESSION["role"] = $role; // Store user role in session

                            // Output SweetAlert2 script for successful login
                            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Welcome, {$firstname}!',
                                            text: 'You have successfully logged in to INVERTO Management System.',
                                        }).then(function() {";

                            // Check the user's role and redirect accordingly
                            if ($role == 1) {
                                echo "window.location = 'admin/dashboard.php';"; // Redirect admin
                            } else {
                                echo "window.location = 'dashboard.php';"; // Redirect regular user
                            }

                            echo "    });
                                    });
                                  </script>";
                            exit;
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if email does not exist
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Handle form errors with SweetAlert2
    if (!empty($email_err) || !empty($password_err)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '" . htmlspecialchars($email_err . ' ' . $password_err) . "',
                    }).then(function() {
                        window.history.back();
                    });
                });
              </script>";
    }

    // Close connection
    $conn->close();
}
?>
