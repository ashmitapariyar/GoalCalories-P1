<?php
/**
 * Insert Calories Data with Duplicate Prevention
 * 
 * This script ensures that only ONE entry per day per user is allowed.
 * - Checks if an entry already exists for today
 * - If exists: Updates the existing entry
 * - If not: Inserts a new entry
 */

require_once '../config/database.php';

// Start session to access session variables
session_start(); 

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
} 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.'
    ]);
    exit();
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

// Validate POST data
if (!isset($_POST['breakfastCalories']) || !isset($_POST['totalCalories'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data received.'
    ]);
    exit();
}

// Get the calories data from the POST request
$breakfastCalories = intval($_POST['breakfastCalories']);
$lunchCalories = intval($_POST['lunchCalories']);
$snackCalories = intval($_POST['snackCalories']);
$dinnerCalories = intval($_POST['dinnerCalories']);
$totalCalories = intval($_POST['totalCalories']);
$dailyCalories = intval($_POST['dailyCalories']);
$surplus = intval($_POST['surplus']);
$deficit = intval($_POST['deficit']);

/**
 * STEP 1: Check if an entry already exists for today
 * Uses DATE() function to compare only the date part, ignoring time
 */
$check_sql = "SELECT id FROM daily_calories 
              WHERE user_id = ? 
              AND DATE(created_at) = CURDATE()";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    /**
     * STEP 2A: Entry exists - UPDATE the existing record
     */
    $existing_row = $check_result->fetch_assoc();
    $existing_id = $existing_row['id'];
    
    $update_sql = "UPDATE daily_calories 
                   SET breakfastCalories = ?, 
                       lunchCalories = ?, 
                       snackCalories = ?, 
                       dinnerCalories = ?, 
                       totalCalories = ?, 
                       dailyCalories = ?, 
                       surplus = ?, 
                       deficit = ?
                   WHERE id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iiiiiiiii", 
        $breakfastCalories, 
        $lunchCalories, 
        $snackCalories, 
        $dinnerCalories, 
        $totalCalories, 
        $dailyCalories, 
        $surplus, 
        $deficit,
        $existing_id
    );
    
    if ($update_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'action' => 'updated',
            'message' => 'Today\'s calorie data updated successfully! You can only have one entry per day.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating data: ' . $update_stmt->error
        ]);
    }
    
    $update_stmt->close();
    
} else {
    /**
     * STEP 2B: No entry exists - INSERT new record
     */
    $insert_sql = "INSERT INTO daily_calories 
                   (user_id, breakfastCalories, lunchCalories, snackCalories, dinnerCalories, totalCalories, dailyCalories, surplus, deficit)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiiiiiiii", 
        $user_id, 
        $breakfastCalories, 
        $lunchCalories, 
        $snackCalories, 
        $dinnerCalories, 
        $totalCalories, 
        $dailyCalories, 
        $surplus, 
        $deficit
    );
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'action' => 'inserted',
            'message' => 'Today\'s calorie data saved successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error inserting data: ' . $insert_stmt->error
        ]);
    }
    
    $insert_stmt->close();
}

// Clean up
$check_stmt->close();
$conn->close();
?>
