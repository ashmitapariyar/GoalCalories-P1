
<?php
session_start();
$con = mysqli_connect('localhost', 'root', '', 'project1');  // Update credentials as per your setup

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query to find user with matching email and password
    $query = "SELECT * FROM registration WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if password matches
        if ($password === $user['password']) {  // Replace with password_verify() if passwords are hashed
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.html");
            exit();
        } else {
            $_SESSION['msg'] = "Invalid email or password!";
            header("Location: login.html");

        }
    } else {
        $_SESSION['msg'] = "Invalid email or password!";
        header("Location: login.html");

    }
}
?>

