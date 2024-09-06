<?php
// Include database connection
include 'includes/db.php';

// Initialize variables
$firstname = $lastname = $username = $email = $store_name = $store_category = $phone_number = $password = "";
$firstname_err = $lastname_err = $username_err = $email_err = $store_name_err = $store_category_err = $phone_number_err = $password_err = "";

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["firstname"]))) {
        $firstname_err = "Please enter your first name.";
    } else {
        // Convert to uppercase
        $firstname = strtoupper(trim($_POST["firstname"]));
    }

    // Validate last name
    if (empty(trim($_POST["lastname"]))) {
        $lastname_err = "Please enter your last name.";
    } else {
        // Convert to uppercase
        $lastname = strtoupper(trim($_POST["lastname"]));
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please choose a username.";
    } else {
        // Convert to uppercase
        $username = strtoupper(trim($_POST["username"]));
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Check if the email is already in use
            $sql = "SELECT id FROM users WHERE email = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $email_err = "This email is already registered.";
                }

                $stmt->close();
            }
        }
    }

    // Validate store name
    if (empty(trim($_POST["store_name"]))) {
        $store_name_err = "Please enter your store name.";
    } else {
        // Convert to uppercase
        $store_name = strtoupper(trim($_POST["store_name"]));
    }

    // Validate store category
    if (empty(trim($_POST["store_category"]))) {
        $store_category_err = "Please select a store category.";
    } else {
        $store_category = trim($_POST["store_category"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter your phone number.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please choose a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting into the database
    if (empty($firstname_err) && empty($lastname_err) && empty($username_err) && empty($email_err) && empty($store_name_err) && empty($store_category_err) && empty($phone_number_err) && empty($password_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (firstname, lastname, username, email, store_name, store_category, phone_number, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement
            $stmt->bind_param("ssssssss", $param_firstname, $param_lastname, $param_username, $param_email, $param_store_name, $param_store_category, $param_phone_number, $param_password);

            // Set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_username = $username;
            $param_email = $email;
            $param_store_name = $store_name;
            $param_store_category = $store_category;
            $param_phone_number = $phone_number;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Create a password hash

            // Execute the statement
            if ($stmt->execute()) {
                // Output SweetAlert2 script for successful registration
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful',
                                text: 'You have successfully registered. Please log in to continue.',
                            }).then(function() {
                                window.location = 'index.php';
                            });
                        });
                      </script>";
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Handle form errors with SweetAlert2
    if (!empty($firstname_err) || !empty($lastname_err) || !empty($username_err) || !empty($email_err) || !empty($store_name_err) || !empty($store_category_err) || !empty($phone_number_err) || !empty($password_err)) {
        $errors = implode(' ', array_filter([
            $firstname_err,
            $lastname_err,
            $username_err,
            $email_err,
            $store_name_err,
            $store_category_err,
            $phone_number_err,
            $password_err
        ]));

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '" . htmlspecialchars($errors) . "',
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
