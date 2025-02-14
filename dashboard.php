<?php
session_start();

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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message
    $_SESSION['msg'] = "Please log in first.";
    header("Location: login.php");
    exit();
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Query to fetch the user's calorie data
$sql = "SELECT * FROM daily_calories WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for JSON response
$calorieData = [];
while ($row = $result->fetch_assoc()) {
    $calorieData[] = $row;
}

// Send data as JSON
header('Content-Type: application/json');
echo json_encode($calorieData);

// Close connection
$stmt->close();
$conn->close();
?>
