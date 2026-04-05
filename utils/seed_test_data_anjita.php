<?php
/**
 * Seed Test Data for Anjita Nepal
 * Creates 15 days of realistic calorie tracking data for existing user
 * 
 * Usage: Run this file in browser: http://localhost/GoalCalories-P1/utils/seed_test_data_anjita.php
 */

// Handle both CLI and web execution
$configPath = __DIR__ . '/../config/database.php';
if (!file_exists($configPath)) {
    $configPath = dirname(__FILE__) . '/../config/database.php';
}
require_once $configPath;

// Start output
echo "<!DOCTYPE html><html><head><title>Seed Test Data</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5}";
echo ".success{color:#10b981;background:#d1fae5;padding:10px;margin:5px 0;border-radius:5px}";
echo ".error{color:#ef4444;background:#fee2e2;padding:10px;margin:5px 0;border-radius:5px}";
echo ".info{color:#3b82f6;background:#dbeafe;padding:10px;margin:5px 0;border-radius:5px}";
echo "h2{color:#1f2937}</style></head><body>";
echo "<h2>🌱 Seeding Test Data for Anjita Nepal</h2>";

try {
    $conn = getDatabaseConnection();
    
    // Step 1: Find the existing user
    echo "<div class='info'><strong>Step 1:</strong> Looking for existing user 'Anjita Nepal'...</div>";
    
    $checkUser = "SELECT id, fullname, email FROM register WHERE fullname LIKE '%Anjita%Nepal%'";
    $result = mysqli_query($conn, $checkUser);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['id'];
        $fullname = $user['fullname'];
        $email = $user['email'];
        echo "<div class='success'>✓ Found user: $fullname (ID: $user_id, Email: $email)</div>";
    } else {
        throw new Exception("User 'Anjita Nepal' not found in database. Please check the name and try again.");
    }
    
    // Step 2: Clear existing data for this user
    echo "<div class='info'><strong>Step 2:</strong> Clearing existing calorie data...</div>";
    $deleteQuery = "DELETE FROM daily_calories WHERE user_id = $user_id";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    $deletedRows = mysqli_affected_rows($conn);
    echo "<div class='success'>✓ Cleared $deletedRows old records</div>";
    
    // Step 3: Generate 15 days of realistic data
    echo "<div class='info'><strong>Step 3:</strong> Generating 15 days of calorie data...</div>";
    
    // Realistic calorie patterns for a person with 2200 daily goal
    $dailyGoal = 2200;
    
    // Create varied but realistic data with interesting patterns
    $testData = [
        // Day 1-3: Good balanced start
        ['breakfast' => 560, 'lunch' => 720, 'snacks' => 180, 'dinner' => 740, 'daily' => 2200],
        ['breakfast' => 520, 'lunch' => 680, 'snacks' => 200, 'dinner' => 780, 'daily' => 2200],
        ['breakfast' => 580, 'lunch' => 700, 'snacks' => 150, 'dinner' => 720, 'daily' => 2200],
        
        // Day 4-6: Weekend pattern - higher intake, irregular timing
        ['breakfast' => 350, 'lunch' => 820, 'snacks' => 350, 'dinner' => 850, 'daily' => 2200],
        ['breakfast' => 200, 'lunch' => 900, 'snacks' => 380, 'dinner' => 880, 'daily' => 2200], // Late breakfast
        ['breakfast' => 620, 'lunch' => 750, 'snacks' => 320, 'dinner' => 780, 'daily' => 2200],
        
        // Day 7-9: Back to routine, good balance
        ['breakfast' => 540, 'lunch' => 690, 'snacks' => 160, 'dinner' => 750, 'daily' => 2200],
        ['breakfast' => 510, 'lunch' => 710, 'snacks' => 170, 'dinner' => 730, 'daily' => 2200],
        ['breakfast' => 550, 'lunch' => 680, 'snacks' => 140, 'dinner' => 760, 'daily' => 2200],
        
        // Day 10-12: Low breakfast pattern (skipping/minimal)
        ['breakfast' => 180, 'lunch' => 800, 'snacks' => 280, 'dinner' => 870, 'daily' => 2200],
        ['breakfast' => 220, 'lunch' => 820, 'snacks' => 260, 'dinner' => 850, 'daily' => 2200],
        ['breakfast' => 250, 'lunch' => 780, 'snacks' => 240, 'dinner' => 820, 'daily' => 2200],
        
        // Day 13-15: Recent improvement, better distribution
        ['breakfast' => 500, 'lunch' => 720, 'snacks' => 180, 'dinner' => 740, 'daily' => 2200],
        ['breakfast' => 530, 'lunch' => 690, 'snacks' => 160, 'dinner' => 750, 'daily' => 2200],
        ['breakfast' => 520, 'lunch' => 710, 'snacks' => 170, 'dinner' => 730, 'daily' => 2200],
    ];
    
    $insertedDays = 0;
    $today = time();
    
    foreach ($testData as $index => $day) {
        // Calculate date (going backwards from today)
        $daysAgo = 14 - $index; // 14 days ago to today
        $date = date('Y-m-d H:i:s', strtotime("-$daysAgo days", $today));
        
        $breakfast = $day['breakfast'];
        $lunch = $day['lunch'];
        $snacks = $day['snacks'];
        $dinner = $day['dinner'];
        $dailyGoal = $day['daily'];
        
        $totalCalories = $breakfast + $lunch + $snacks + $dinner;
        $surplus = max(0, $totalCalories - $dailyGoal);
        $deficit = max(0, $dailyGoal - $totalCalories);
        
        $insertQuery = "INSERT INTO daily_calories 
                       (user_id, breakfastCalories, lunchCalories, snackCalories, dinnerCalories, 
                        totalCalories, dailyCalories, surplus, deficit, created_at) 
                       VALUES 
                       ($user_id, $breakfast, $lunch, $snacks, $dinner, 
                        $totalCalories, $dailyGoal, $surplus, $deficit, '$date')";
        
        if (mysqli_query($conn, $insertQuery)) {
            $insertedDays++;
            $dayLabel = date('M j', strtotime($date));
            $status = $totalCalories > $dailyGoal ? "↑ Surplus: +$surplus cal" : ($totalCalories < $dailyGoal ? "↓ Deficit: -$deficit cal" : "✓ Perfect!");
            echo "<div class='success'>✓ Day " . ($index + 1) . " ($dayLabel): Total $totalCalories cal | $status</div>";
        } else {
            echo "<div class='error'>✗ Failed to insert day " . ($index + 1) . ": " . mysqli_error($conn) . "</div>";
        }
    }
    
    // Summary
    echo "<br><h3>📊 Summary</h3>";
    echo "<div class='success'><strong>✓ Successfully added $insertedDays days of data for $fullname!</strong></div>";
    
    // Calculate statistics
    $statsQuery = "SELECT 
                    COUNT(*) as total_days,
                    AVG(breakfastCalories) as avg_breakfast,
                    AVG(lunchCalories) as avg_lunch,
                    AVG(snackCalories) as avg_snacks,
                    AVG(dinnerCalories) as avg_dinner,
                    AVG(totalCalories) as avg_total,
                    SUM(surplus) as total_surplus,
                    SUM(deficit) as total_deficit,
                    MIN(totalCalories) as min_total,
                    MAX(totalCalories) as max_total
                   FROM daily_calories 
                   WHERE user_id = $user_id";
    
    $statsResult = mysqli_query($conn, $statsQuery);
    if ($stats = mysqli_fetch_assoc($statsResult)) {
        echo "<div class='info'>";
        echo "<strong>📈 Statistics for $fullname:</strong><br><br>";
        echo "📅 <strong>Total Days Tracked:</strong> " . $stats['total_days'] . " days<br>";
        echo "📊 <strong>Average Daily Intake:</strong> " . round($stats['avg_total']) . " cal<br>";
        echo "⬇️ <strong>Lowest Day:</strong> " . $stats['min_total'] . " cal<br>";
        echo "⬆️ <strong>Highest Day:</strong> " . $stats['max_total'] . " cal<br><br>";
        echo "<strong>Category Averages:</strong><br>";
        echo "🍳 Breakfast: " . round($stats['avg_breakfast']) . " cal (Recommended: ~25-30% = 550-660 cal)<br>";
        echo "🍽️ Lunch: " . round($stats['avg_lunch']) . " cal (Recommended: ~35-40% = 770-880 cal)<br>";
        echo "🍪 Snacks: " . round($stats['avg_snacks']) . " cal (Recommended: ~10-15% = 220-330 cal)<br>";
        echo "🍕 Dinner: " . round($stats['avg_dinner']) . " cal (Recommended: ~25-30% = 550-660 cal)<br><br>";
        echo "<strong>Cumulative:</strong><br>";
        echo "📈 Total Surplus: " . $stats['total_surplus'] . " cal<br>";
        echo "📉 Total Deficit: " . $stats['total_deficit'] . " cal<br>";
        echo "</div>";
        
        // Pattern insights
        $avgBreakfast = round($stats['avg_breakfast']);
        echo "<div class='info'><strong>🔍 Pattern Analysis:</strong><br>";
        if ($avgBreakfast < 450) {
            echo "⚠️ Low breakfast intake detected (avg: $avgBreakfast cal). The 7-day analysis will likely recommend increasing breakfast.<br>";
        } else {
            echo "✓ Breakfast intake is healthy (avg: $avgBreakfast cal).<br>";
        }
        echo "📊 Data includes weekend patterns, low breakfast days, and recent improvements for realistic testing.<br>";
        echo "</div>";
    }
    
    echo "<br><div class='success'><strong>✓ All done! Next steps:</strong><br>";
    echo "1. Login with email: <strong>$email</strong><br>";
    echo "2. Go to Dashboard → Reports to see all data<br>";
    echo "3. Try \"Analyze My Progress\" for 7-day analysis<br>";
    echo "4. Check \"Get Food Recommendation\" for personalized suggestions<br>";
    echo "5. View charts and trends in the report section</div>";
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "<div class='error'><strong>❌ Error:</strong> " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
