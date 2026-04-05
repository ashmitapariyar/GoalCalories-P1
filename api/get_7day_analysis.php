<?php
/**
 * 7-Day Moving Average Calorie Analysis
 * 
 * This script calculates the 7-day moving average of calorie intake
 * for the currently logged-in user and compares it with their daily requirement.
 * 
 * Algorithm:
 * 1. Fetch last 7 days of calorie data (or fewer if not available)
 * 2. Calculate total and average calories
 * 3. Compare average with daily requirement
 * 4. Return analysis result as JSON
 */

require_once '../config/database.php';

// Start session and establish database connection
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

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

/**
 * STEP 1: Fetch last 7 days of calorie data
 * Uses prepared statement to prevent SQL injection
 * Orders by date descending and limits to 7 records
 */
$sql = "SELECT totalCalories, dailyCalories, created_at 
        FROM daily_calories 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 7";

// Prepare statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Query preparation failed: ' . $conn->error
    ]);
    exit();
}

// Bind parameter and execute
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

/**
 * STEP 2: Process the data
 * Calculate total calories and count days
 */
$total_calories = 0;
$day_count = 0;
$daily_requirement = 0;
$data_records = [];

while ($row = $result->fetch_assoc()) {
    $total_calories += $row['totalCalories'];
    $daily_requirement = $row['dailyCalories']; // Get user's daily requirement
    $day_count++;
    
    // Store records for detailed display (optional)
    $data_records[] = [
        'date' => $row['created_at'],
        'calories' => $row['totalCalories'],
        'requirement' => $row['dailyCalories']
    ];
}

/**
 * STEP 3: Calculate 7-day moving average
 * Handle edge case: if no data exists
 */
if ($day_count == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'No calorie data found. Please track your meals first.',
        'day_count' => 0
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Calculate average (prevents division by zero)
$average_calories = $total_calories / $day_count;

/**
 * STEP 4: Compare with daily requirement
 * Determine intake status
 */
$intake_status = '';
$status_class = '';
$recommendation = '';

if ($average_calories > $daily_requirement) {
    $intake_status = 'Over Intake';
    $status_class = 'over-intake';
    $difference = $average_calories - $daily_requirement;
    $recommendation = "You're consuming " . round($difference, 2) . " calories more than your daily requirement. Consider reducing portion sizes or choosing lower-calorie alternatives.";
} elseif ($average_calories < $daily_requirement) {
    $intake_status = 'Under Intake';
    $status_class = 'under-intake';
    $difference = $daily_requirement - $average_calories;
    $recommendation = "You're consuming " . round($difference, 2) . " calories less than your daily requirement. Consider adding nutritious snacks or increasing portion sizes.";
} else {
    $intake_status = 'Balanced Intake';
    $status_class = 'balanced-intake';
    $recommendation = "Perfect! You're maintaining a balanced calorie intake. Keep up the good work!";
}

/**
 * STEP 5: Prepare response
 * Return comprehensive analysis as JSON
 */
$response = [
    'success' => true,
    'analysis' => [
        'day_count' => $day_count,
        'total_calories' => round($total_calories, 2),
        'average_calories' => round($average_calories, 2),
        'daily_requirement' => round($daily_requirement, 2),
        'intake_status' => $intake_status,
        'status_class' => $status_class,
        'recommendation' => $recommendation,
        'data_records' => $data_records
    ],
    'message' => 'Analysis completed successfully'
];

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Clean up
$stmt->close();
$conn->close();
?>
