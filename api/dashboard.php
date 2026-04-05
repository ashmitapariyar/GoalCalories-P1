<?php
/**
 * Dashboard Data API
 * Fetches user's calorie history
 */

require_once '../config/database.php';

session_start();

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message
    $_SESSION['msg'] = "Please log in first.";
    header("Location: ../auth/login.php");
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
