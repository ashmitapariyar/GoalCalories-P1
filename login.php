<?php
session_start();
$con = mysqli_connect('localhost', 'root', '', 'project1');  // Update credentials as per your setup

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query to find user with matching email
    $query = "SELECT * FROM registration WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Use password_verify to check if entered password matches the hashed one in the database
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  // Store user_id in session
            $_SESSION['email'] = $user['email'];  // Optionally store email as well
            header("Location: dashboard.html");  // Redirect to dashboard (not dashboard.html)
            exit();
        } else {
            $_SESSION['msg'] = "Invalid email or password!";
            header("Location: login.php");  // Stay on login page if authentication fails
            exit();
        }
    } else {
        $_SESSION['msg'] = "Invalid email or password!";
        header("Location: login.php");  // Stay on login page if no user found
        exit();
    }
}
?>
