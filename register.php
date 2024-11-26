<?php
// Connect to the database
$con = mysqli_connect('localhost', 'root', '', 'project1');

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
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



// Insert into the database
$sql = "INSERT INTO registration (fullname, phonenumber, email, password,confirmpassword) 
        VALUES ('$fullname', '$phone_number', '$email', '$password','$confirm_password' )";

$res = mysqli_query($con, $sql);

if (!$res) {
    die("Insertion failed: " . mysqli_error($con));
} else {
    // Redirect to home page after successful registration
    header("Location: home.html");
    exit();
}

// Close the connection
mysqli_close($con);

?>
