<?php
/**
 * Seed Test Data for Alina Panta
 * Creates 15 days of realistic calorie tracking data
 * 
 * Usage: Run this file in browser: http://localhost/GoalCalories-P1/utils/seed_test_data_anita.php
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
echo "<h2>🌱 Seeding Test Data for Alina Panta</h2>";

try {
    $conn = getDatabaseConnection();
    
    // Step 1: Check if user exists, if not create
    echo "<div class='info'><strong>Step 1:</strong> Checking for user 'Alina Panta'...</div>";
    
    $email = 'alina.panta@test.com';
    $checkUser = "SELECT id FROM register WHERE email = '$email'";
    $result = mysqli_query($conn, $checkUser);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['id'];
        echo "<div class='success'>✓ User exists with ID: $user_id</div>";
    } else {
        // Create user
        echo "<div class='info'>Creating new user...</div>";
        $fullname = 'Alina Panta';
        $phonenumber = '9841234567';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $insertUser = "INSERT INTO register (fullname, phonenumber, email, password) 
                       VALUES ('$fullname', '$phonenumber', '$email', '$password')";
        
        if (mysqli_query($conn, $insertUser)) {
            $user_id = mysqli_insert_id($conn);
            echo "<div class='success'>✓ User created successfully! ID: $user_id</div>";
            echo "<div class='info'><strong>Login Credentials:</strong><br>Email: $email<br>Password: password123</div>";
        } else {
            throw new Exception("Failed to create user: " . mysqli_error($conn));
        }
    }
    
    // Step 2: Clear existing data for this user
    echo "<div class='info'><strong>Step 2:</strong> Clearing existing calorie data...</div>";
    $deleteQuery = "DELETE FROM daily_calories WHERE user_id = $user_id";
    mysqli_query($conn, $deleteQuery);
    echo "<div class='success'>✓ Cleared old data</div>";
    
    // Step 3: Generate 15 days of realistic data
    echo "<div class='info'><strong>Step 3:</strong> Generating 15 days of calorie data...</div>";
    
    // Realistic calorie patterns for a person with 2200 daily goal
    $dailyGoal = 2200;
    
    // Create varied but realistic data
    $testData = [
        // Day 1-3: Starting well, slightly under goal
        ['breakfast' => 520, 'lunch' => 680, 'snacks' => 150, 'dinner' => 720, 'daily' => 2200],
        ['breakfast' => 480, 'lunch' => 720, 'snacks' => 180, 'dinner' => 680, 'daily' => 2200],
        ['breakfast' => 550, 'lunch' => 650, 'snacks' => 120, 'dinner' => 750, 'daily' => 2200],
        
        // Day 4-6: Weekend, higher intake
        ['breakfast' => 620, 'lunch' => 780, 'snacks' => 320, 'dinner' => 820, 'daily' => 2200],
        ['breakfast' => 180, 'lunch' => 850, 'snacks' => 280, 'dinner' => 920, 'daily' => 2200], // Skipped breakfast
        ['breakfast' => 580, 'lunch' => 720, 'snacks' => 380, 'dinner' => 780, 'daily' => 2200],
        
        // Day 7-9: Back on track
        ['breakfast' => 540, 'lunch' => 670, 'snacks' => 140, 'dinner' => 700, 'daily' => 2200],
        ['breakfast' => 510, 'lunch' => 690, 'snacks' => 160, 'dinner' => 720, 'daily' => 2200],
        ['breakfast' => 560, 'lunch' => 650, 'snacks' => 130, 'dinner' => 710, 'daily' => 2200],
        
        // Day 10-12: Low breakfast pattern
        ['breakfast' => 220, 'lunch' => 750, 'snacks' => 200, 'dinner' => 880, 'daily' => 2200],
        ['breakfast' => 180, 'lunch' => 820, 'snacks' => 250, 'dinner' => 850, 'daily' => 2200],
        ['breakfast' => 240, 'lunch' => 780, 'snacks' => 220, 'dinner' => 840, 'daily' => 2200],
        
        // Day 13-15: Recent days, improving
        ['breakfast' => 480, 'lunch' => 710, 'snacks' => 180, 'dinner' => 760, 'daily' => 2200],
        ['breakfast' => 520, 'lunch' => 680, 'snacks' => 150, 'dinner' => 740, 'daily' => 2200],
        ['breakfast' => 500, 'lunch' => 700, 'snacks' => 170, 'dinner' => 730, 'daily' => 2200],
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
            $status = $totalCalories > $dailyGoal ? "↑ Surplus: +$surplus" : "↓ Deficit: -$deficit";
            echo "<div class='success'>✓ Day " . ($index + 1) . " ($date): Total $totalCalories cal | $status</div>";
        } else {
            echo "<div class='error'>✗ Failed to insert day " . ($index + 1) . ": " . mysqli_error($conn) . "</div>";
        }
    }
    
    // Summary
    echo "<br><h3>📊 Summary</h3>";
    echo "<div class='success'><strong>✓ Successfully added $insertedDays days of data for Alina Panta!</strong></div>";
    
    // Calculate statistics
    $statsQuery = "SELECT 
                    COUNT(*) as total_days,
                    AVG(breakfastCalories) as avg_breakfast,
                    AVG(lunchCalories) as avg_lunch,
                    AVG(snackCalories) as avg_snacks,
                    AVG(dinnerCalories) as avg_dinner,
                    AVG(totalCalories) as avg_total,
                    SUM(surplus) as total_surplus,
                    SUM(deficit) as total_deficit
                   FROM daily_calories 
                   WHERE user_id = $user_id";
    
    $statsResult = mysqli_query($conn, $statsQuery);
    if ($stats = mysqli_fetch_assoc($statsResult)) {
        echo "<div class='info'>";
        echo "<strong>Statistics:</strong><br>";
        echo "📅 Total Days: " . $stats['total_days'] . "<br>";
        echo "🍳 Avg Breakfast: " . round($stats['avg_breakfast']) . " cal<br>";
        echo "🍽️ Avg Lunch: " . round($stats['avg_lunch']) . " cal<br>";
        echo "🍪 Avg Snacks: " . round($stats['avg_snacks']) . " cal<br>";
        echo "🍕 Avg Dinner: " . round($stats['avg_dinner']) . " cal<br>";
        echo "📊 Avg Total: " . round($stats['avg_total']) . " cal<br>";
        echo "📈 Total Surplus: " . $stats['total_surplus'] . " cal<br>";
        echo "📉 Total Deficit: " . $stats['total_deficit'] . " cal<br>";
        echo "</div>";
    }
    
    echo "<br><div class='success'><strong>✓ All done! You can now:</strong><br>";
    echo "1. Login with email: $email (password: password123)<br>";
    echo "2. View reports in the dashboard<br>";
    echo "3. Test the 7-day analysis feature<br>";
    echo "4. Check food recommendations based on eating patterns</div>";
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "<div class='error'><strong>Error:</strong> " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
