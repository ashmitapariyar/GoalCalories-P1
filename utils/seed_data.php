<?php
/**
 * Database Seeding Script
 * 
 * Purpose: Clear existing calorie data and populate with test data
 * - birat11@gmail.com: 14 days of data
 * - ashmita11@gmail.com: 7 days of data
 */

require_once '../config/database.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    die("<h2>Database Connection Error</h2><p>" . $e->getMessage() . "</p>");
}

echo "<h2>Database Seeding Process</h2>";
echo "<hr>";

/**
 * STEP 1: Clear all existing data from daily_calories table
 */
echo "<h3>Step 1: Clearing existing data...</h3>";
$clear_sql = "DELETE FROM daily_calories";
if ($conn->query($clear_sql) === TRUE) {
    echo "✓ All existing calorie data cleared successfully.<br><br>";
} else {
    echo "✗ Error clearing data: " . $conn->error . "<br><br>";
}

/**
 * STEP 2: Get user IDs from email addresses
 */
echo "<h3>Step 2: Fetching user IDs...</h3>";

// Get user ID for birat11@gmail.com
$email1 = "birat11@gmail.com";
$stmt1 = $conn->prepare("SELECT id FROM register WHERE email = ?");
$stmt1->bind_param("s", $email1);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1->num_rows > 0) {
    $user1 = $result1->fetch_assoc();
    $user1_id = $user1['id'];
    echo "✓ Found user: $email1 (ID: $user1_id)<br>";
} else {
    die("✗ User $email1 not found in database. Please register this user first.<br>");
}

// Get user ID for ashmita11@gmail.com
$email2 = "ashmita11@gmail.com";
$stmt2 = $conn->prepare("SELECT id FROM register WHERE email = ?");
$stmt2->bind_param("s", $email2);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    $user2 = $result2->fetch_assoc();
    $user2_id = $user2['id'];
    echo "✓ Found user: $email2 (ID: $user2_id)<br><br>";
} else {
    die("✗ User $email2 not found in database. Please register this user first.<br>");
}

/**
 * STEP 3: Generate realistic calorie data
 * Function to generate varied but realistic calorie data
 */
function generateCalorieData($dailyRequirement, $variation = 200) {
    // Random variation to make data realistic
    $totalCalories = $dailyRequirement + rand(-$variation, $variation);
    
    // Distribute calories across meals (realistic proportions)
    $breakfast = rand(250, 450);
    $lunch = rand(400, 700);
    $snacks = rand(100, 350);
    $dinner = rand(350, 650);
    
    // Adjust to match total
    $mealTotal = $breakfast + $lunch + $snacks + $dinner;
    $ratio = $totalCalories / $mealTotal;
    
    $breakfast = round($breakfast * $ratio);
    $lunch = round($lunch * $ratio);
    $snacks = round($snacks * $ratio);
    $dinner = round($dinner * $ratio);
    
    // Recalculate total after rounding
    $totalCalories = $breakfast + $lunch + $snacks + $dinner;
    
    // Calculate surplus/deficit
    $surplus = 0;
    $deficit = 0;
    if ($totalCalories > $dailyRequirement) {
        $surplus = $totalCalories - $dailyRequirement;
    } else if ($totalCalories < $dailyRequirement) {
        $deficit = $dailyRequirement - $totalCalories;
    }
    
    return [
        'breakfast' => $breakfast,
        'lunch' => $lunch,
        'snacks' => $snacks,
        'dinner' => $dinner,
        'total' => $totalCalories,
        'surplus' => $surplus,
        'deficit' => $deficit
    ];
}

/**
 * STEP 4: Seed data for birat11@gmail.com (14 days)
 */
echo "<h3>Step 3: Seeding data for $email1 (14 days)...</h3>";
$dailyRequirement1 = 2200; // Male, moderate activity
$insert_sql = "INSERT INTO daily_calories (user_id, breakfastCalories, lunchCalories, snackCalories, dinnerCalories, totalCalories, dailyCalories, surplus, deficit, created_at) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);

$successCount1 = 0;
for ($i = 13; $i >= 0; $i--) { // 14 days (0 to 13 days ago)
    $data = generateCalorieData($dailyRequirement1, 300);
    
    // Calculate date (going backwards from today)
    $date = date('Y-m-d H:i:s', strtotime("-$i days"));
    
    $stmt->bind_param("iiiiiiiiis", 
        $user1_id,
        $data['breakfast'],
        $data['lunch'],
        $data['snacks'],
        $data['dinner'],
        $data['total'],
        $dailyRequirement1,
        $data['surplus'],
        $data['deficit'],
        $date
    );
    
    if ($stmt->execute()) {
        $successCount1++;
        echo "✓ Day " . (14 - $i) . ": " . date('M d, Y', strtotime("-$i days")) . 
             " - Total: {$data['total']} cal (B: {$data['breakfast']}, L: {$data['lunch']}, S: {$data['snacks']}, D: {$data['dinner']})<br>";
    } else {
        echo "✗ Error inserting data for day $i: " . $stmt->error . "<br>";
    }
}
echo "<strong>✓ Successfully inserted $successCount1 days of data for $email1</strong><br><br>";

/**
 * STEP 5: Seed data for ashmita11@gmail.com (7 days)
 */
echo "<h3>Step 4: Seeding data for $email2 (7 days)...</h3>";
$dailyRequirement2 = 1800; // Female, moderate activity

$successCount2 = 0;
for ($i = 6; $i >= 0; $i--) { // 7 days (0 to 6 days ago)
    $data = generateCalorieData($dailyRequirement2, 250);
    
    // Calculate date (going backwards from today)
    $date = date('Y-m-d H:i:s', strtotime("-$i days"));
    
    $stmt->bind_param("iiiiiiiiis", 
        $user2_id,
        $data['breakfast'],
        $data['lunch'],
        $data['snacks'],
        $data['dinner'],
        $data['total'],
        $dailyRequirement2,
        $data['surplus'],
        $data['deficit'],
        $date
    );
    
    if ($stmt->execute()) {
        $successCount2++;
        echo "✓ Day " . (7 - $i) . ": " . date('M d, Y', strtotime("-$i days")) . 
             " - Total: {$data['total']} cal (B: {$data['breakfast']}, L: {$data['lunch']}, S: {$data['snacks']}, D: {$data['dinner']})<br>";
    } else {
        echo "✗ Error inserting data for day $i: " . $stmt->error . "<br>";
    }
}
echo "<strong>✓ Successfully inserted $successCount2 days of data for $email2</strong><br><br>";

/**
 * STEP 6: Display summary
 */
echo "<hr>";
echo "<h3>Summary</h3>";
echo "<ul>";
echo "<li>✓ Database cleared successfully</li>";
echo "<li>✓ $successCount1 days of data seeded for <strong>$email1</strong> (Daily Requirement: $dailyRequirement1 cal)</li>";
echo "<li>✓ $successCount2 days of data seeded for <strong>$email2</strong> (Daily Requirement: $dailyRequirement2 cal)</li>";
echo "</ul>";

echo "<h4>Next Steps:</h4>";
echo "<ol>";
echo "<li>Login as <strong>$email1</strong> and test the 7-Day Analysis (should show 7-day average)</li>";
echo "<li>Login as <strong>$email2</strong> and test the 7-Day Analysis (should show 7-day average)</li>";
echo "<li>Verify that the moving average calculation is working correctly</li>";
echo "</ol>";

echo "<br><a href='../pages/dashboard.html' style='background-color: #007BFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";

// Close connections
$stmt->close();
$stmt1->close();
$stmt2->close();
$conn->close();

echo "<br><br><strong>✓ Database seeding completed successfully!</strong>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background-color: #f4f4f9;
    }
    h2 { color: #007BFF; }
    h3 { color: #0056b3; margin-top: 20px; }
    ul, ol { line-height: 1.8; }
    hr { margin: 20px 0; border: 1px solid #ddd; }
</style>
