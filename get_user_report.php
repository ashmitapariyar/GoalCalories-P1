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

// Assuming you're using session-based login
session_start();  // Start session to access session variables
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

// Query to get the user's data from the database
$sql = "SELECT * FROM daily_calories WHERE user_id = ?";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Get results
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return the data as JSON
echo json_encode($data);

// Close the connection
$stmt->close();
$conn->close();
?>
