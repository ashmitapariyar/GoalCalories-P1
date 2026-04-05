<?php
/**
 * User Login Handler
 * Authenticates users and creates session
 */

require_once '../config/database.php';

session_start();

// If accessed directly via GET, redirect to login page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.html");
    exit();
}

try {
    $con = getDatabaseConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];  // Don't escape password for verification

    // Query to find user with matching email
    $query = "SELECT * FROM register WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Use password_verify to check if entered password matches the hashed one in the database
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  // Store user_id in session
            $_SESSION['email'] = $user['email'];  // Optionally store email as well
            $_SESSION['fullname'] = $user['fullname'];  // Store name for display
            
            header("Location: ../pages/dashboard.html");  // Redirect to dashboard
            exit();
        } else {
            $_SESSION['msg'] = "Invalid email or password!";
            header("Location: ../pages/login.html");  // Go back to login page
            exit();
        }
    } else {
        $_SESSION['msg'] = "Invalid email or password!";
        header("Location: ../pages/login.html");  // Go back to login page
        exit();
    }
}
?>
