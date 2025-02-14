<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calorie_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to access session variables
session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

// Get the calories data from the POST request
$breakfastCalories = $_POST['breakfastCalories'];
$lunchCalories = $_POST['lunchCalories'];
$snackCalories = $_POST['snackCalories'];
$dinnerCalories = $_POST['dinnerCalories'];
$totalCalories = $_POST['totalCalories'];
$dailyCalories = $_POST['dailyCalories'];
$surplus = $_POST['surplus'];
$deficit = $_POST['deficit'];

// Prepare the SQL query to insert the data
$sql = "INSERT INTO daily_calories (user_id, breakfastCalories, lunchCalories, snackCalories, dinnerCalories, totalCalories, dailyCalories, surplus, deficit)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("iiiiiiiii", $user_id, $breakfastCalories, $lunchCalories, $snackCalories, $dinnerCalories, $totalCalories, $dailyCalories, $surplus, $deficit);

// Execute the query
if ($stmt->execute()) {
    echo "Calories data inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
