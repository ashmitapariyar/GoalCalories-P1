<?php
/**
 * User Registration Handler
 * Creates new user account with encrypted password
 */

require_once '../config/database.php';

// If accessed directly via GET, redirect to registration page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/registration.html");
    exit();
}

try {
    $con = getDatabaseConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve form data
$fullname = $_POST['fullname'];
$phone_number = $_POST['phone_number'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Check if passwords match
if ($password !== $confirm_password) {
    die("Passwords do not match!");
}

// Hash the password for secure storage
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into the database
$sql = "INSERT INTO register (fullname, phonenumber, email, password, confirmpassword) 
        VALUES ('$fullname', '$phone_number', '$email', '$hashed_password', '$confirm_password')";

$res = mysqli_query($con, $sql);

if (!$res) {
    die("Insertion failed: " . mysqli_error($con));
} else {
    // Redirect to home page after successful registration
    header("Location: ../pages/home.html");
    exit();
}

// Close the connection
mysqli_close($con);
?>
