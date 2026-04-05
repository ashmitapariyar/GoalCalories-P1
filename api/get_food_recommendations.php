<?php
/**
 * Food Recommendations API
 * 
 * Algorithm: Category-Based Calorie Analysis with Weighted Scoring
 * 
 * This system analyzes user's eating patterns by category and provides
 * intelligent recommendations to optimize nutrition distribution.
 * 
 * Key Features:
 * 1. Analyzes 7-day average consumption by category
 * 2. Compares against ideal nutritional distribution
 * 3. Identifies deficient categories
 * 4. Generates personalized food recommendations
 * 5. Prioritizes recommendations based on severity of deficit
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
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * ALGORITHM STEP 1: Get 7-Day Calorie Data
 * Retrieves recent eating patterns for analysis
 */
$sql = "SELECT 
            AVG(breakfastCalories) as avg_breakfast,
            AVG(lunchCalories) as avg_lunch,
            AVG(snackCalories) as avg_snacks,
            AVG(dinnerCalories) as avg_dinner,
            AVG(totalCalories) as avg_total,
            AVG(dailyCalories) as avg_daily_goal,
            COUNT(*) as days_tracked
        FROM daily_calories 
        WHERE user_id = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No calorie data found. Please track your meals first.'
    ]);
    exit();
}

$data = $result->fetch_assoc();

// Check if user has tracked enough days
if ($data['days_tracked'] < 1) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Insufficient data. Please track at least one day of meals.'
    ]);
    exit();
}

/**
 * ALGORITHM STEP 2: Calculate Ideal Distribution
 * Based on nutritional science recommendations:
 * - Breakfast: 25-30% of daily calories (fuels morning activities)
 * - Lunch: 30-35% of daily calories (main energy source)
 * - Dinner: 25-30% of daily calories (lighter evening meal)
 * - Snacks: 10-15% of daily calories (healthy snacking)
 */
$avg_total = max($data['avg_total'], 1); // Prevent division by zero
$avg_goal = max($data['avg_daily_goal'], 1);

$ideal_distribution = [
    'breakfast' => 0.275, // 27.5% (middle of 25-30%)
    'lunch' => 0.325,     // 32.5% (middle of 30-35%)
    'dinner' => 0.275,    // 27.5% (middle of 25-30%)
    'snacks' => 0.125     // 12.5% (middle of 10-15%)
];

/**
 * ALGORITHM STEP 3: Calculate Current Distribution
 */
$current_distribution = [
    'breakfast' => $data['avg_breakfast'] / $avg_total,
    'lunch' => $data['avg_lunch'] / $avg_total,
    'dinner' => $data['avg_dinner'] / $avg_total,
    'snacks' => $data['avg_snacks'] / $avg_total
];

/**
 * ALGORITHM STEP 4: Deficit Analysis with Weighted Scoring
 * Calculate how much each category deviates from ideal
 * Negative score = undereating, Positive score = overeating
 */
$category_analysis = [];
$deficient_categories = [];

foreach ($ideal_distribution as $category => $ideal_percentage) {
    $current_percentage = $current_distribution[$category];
    $deviation = $current_percentage - $ideal_percentage;
    $deviation_percentage = ($deviation / $ideal_percentage) * 100;
    
    $ideal_calories = $avg_goal * $ideal_percentage;
    $current_calories = $data['avg_' . $category];
    $calorie_gap = $ideal_calories - $current_calories;
    
    // Calculate severity score (0-100)
    // Higher score = more severe deficit
    $severity_score = 0;
    if ($calorie_gap > 0) {
        $severity_score = min(100, abs($deviation_percentage));
    }
    
    $category_analysis[$category] = [
        'current_calories' => round($current_calories),
        'ideal_calories' => round($ideal_calories),
        'current_percentage' => round($current_percentage * 100, 1),
        'ideal_percentage' => round($ideal_percentage * 100, 1),
        'calorie_gap' => round($calorie_gap),
        'deviation_percentage' => round($deviation_percentage, 1),
        'severity_score' => round($severity_score),
        'status' => $calorie_gap > 50 ? 'deficient' : ($calorie_gap < -50 ? 'excess' : 'balanced')
    ];
    
    // Identify deficient categories (need more calories)
    if ($calorie_gap > 50) { // Threshold: 50 calories gap
        $deficient_categories[] = [
            'category' => $category,
            'severity' => $severity_score,
            'gap' => $calorie_gap
        ];
    }
}

// Sort deficient categories by severity (highest first)
usort($deficient_categories, function($a, $b) {
    return $b['severity'] - $a['severity'];
});

/**
 * ALGORITHM STEP 5: Query Food Database
 * Fetch foods from the food_calories table dynamically
 */
$food_query = "SELECT food_id, food_name, calories_per_100g, category 
               FROM food_calories 
               ORDER BY category, calories_per_100g";

$food_stmt = $conn->prepare($food_query);
$food_stmt->execute();
$food_result = $food_stmt->get_result();

// Organize foods by category
$food_database = [
    'breakfast' => [],
    'lunch' => [],
    'dinner' => [],
    'snacks' => []
];

while ($food = $food_result->fetch_assoc()) {
    $category_key = strtolower($food['category']);
    $food_database[$category_key][] = [
        'food_id' => $food['food_id'],
        'name' => $food['food_name'],
        'calories' => $food['calories_per_100g'], // Calories per 100g
        'category' => $food['category']
    ];
}

$food_stmt->close();

/**
 * ALGORITHM STEP 6: Generate Prioritized Recommendations
 * Select appropriate foods based on deficit severity and nutritional needs
 */
$recommendations = [];

foreach ($deficient_categories as $deficiency) {
    $category = $deficiency['category'];
    $gap = $deficiency['gap'];
    $severity = $deficiency['severity'];
    
    // Get food suggestions for this category from database
    $available_foods = $food_database[$category] ?? [];
    
    // If no foods in database for this category, skip
    if (empty($available_foods)) {
        continue;
    }
    
    // Select 3 foods that best match the calorie gap
    $selected_foods = [];
    
    // Sort foods by how well they match the gap (using calories per 100g as base)
    usort($available_foods, function($a, $b) use ($gap) {
        // Compare how close each food's calories are to the gap
        $diff_a = abs($a['calories'] - $gap);
        $diff_b = abs($b['calories'] - $gap);
        return $diff_a - $diff_b;
    });
    
    // Select top 3 recommendations
    for ($i = 0; $i < min(3, count($available_foods)); $i++) {
        $selected_foods[] = $available_foods[$i];
    }
    
    $recommendations[] = [
        'category' => ucfirst($category),
        'severity' => $severity_level = $severity > 30 ? 'high' : ($severity > 15 ? 'medium' : 'low'),
        'message' => generateMessage($category, $gap, $category_analysis[$category]),
        'current_avg' => round($category_analysis[$category]['current_calories']),
        'recommended_avg' => round($category_analysis[$category]['ideal_calories']),
        'calorie_gap' => round($gap),
        'suggested_foods' => $selected_foods
    ];
}

/**
 * ALGORITHM STEP 7: General Health Recommendations
 * If all categories are balanced, provide maintenance tips
 */
if (empty($recommendations)) {
    $overall_status = abs($data['avg_total'] - $data['avg_daily_goal']) / $data['avg_daily_goal'];
    
    if ($overall_status < 0.1) { // Within 10% of goal
        $recommendations[] = [
            'category' => 'Overall',
            'severity' => 'none',
            'message' => 'Excellent! Your meal distribution is well balanced. Keep maintaining this healthy eating pattern.',
            'current_avg' => round($data['avg_total']),
            'recommended_avg' => round($data['avg_daily_goal']),
            'calorie_gap' => 0,
            'suggested_foods' => []
        ];
    } else {
        // Generate recommendations for overall improvement
        $recommendations[] = generateOverallRecommendation($data, $category_analysis);
    }
}

/**
 * Helper Function: Generate Context-Aware Messages
 */
function generateMessage($category, $gap, $analysis) {
    $current = $analysis['current_calories'];
    $ideal = $analysis['ideal_calories'];
    $percentage = $analysis['current_percentage'];
    
    $messages = [
        'breakfast' => "Your breakfast intake is low (averaging {$current} cal vs. recommended {$ideal} cal). A nutritious breakfast jumpstarts your metabolism and improves focus throughout the day.",
        'lunch' => "You're consuming fewer lunch calories than ideal ({$current} cal vs. {$ideal} cal). Lunch should be your main energy source for afternoon activities.",
        'dinner' => "Your dinner portions are below recommended levels ({$current} cal vs. {$ideal} cal). Consider adding more protein and vegetables to your evening meal.",
        'snacks' => "You're missing out on healthy snacks ({$current} cal vs. {$ideal} cal). Smart snacking helps maintain energy levels and prevents overeating at main meals."
    ];
    
    return $messages[$category] ?? "Consider increasing your intake for better nutritional balance.";
}

/**
 * Helper Function: Overall Health Recommendation
 */
function generateOverallRecommendation($data, $analysis) {
    $total_diff = $data['avg_total'] - $data['avg_daily_goal'];
    
    if ($total_diff < -200) {
        return [
            'category' => 'Overall Intake',
            'severity' => 'medium',
            'message' => "You're consistently eating below your daily calorie goal. This may lead to fatigue and nutrient deficiencies. Focus on adding nutrient-dense foods throughout the day.",
            'current_avg' => round($data['avg_total']),
            'recommended_avg' => round($data['avg_daily_goal']),
            'calorie_gap' => round(abs($total_diff)),
            'suggested_foods' => []
        ];
    } else if ($total_diff > 200) {
        return [
            'category' => 'Overall Intake',
            'severity' => 'medium',
            'message' => "You're consistently exceeding your daily calorie goal. Consider portion control and choosing lower-calorie alternatives.",
            'current_avg' => round($data['avg_total']),
            'recommended_avg' => round($data['avg_daily_goal']),
            'calorie_gap' => round($total_diff),
            'suggested_foods' => []
        ];
    }
    
    return [
        'category' => 'Maintenance',
        'severity' => 'none',
        'message' => "Your calorie intake is well balanced. Continue your current eating pattern and stay active!",
        'current_avg' => round($data['avg_total']),
        'recommended_avg' => round($data['avg_daily_goal']),
        'calorie_gap' => 0,
        'suggested_foods' => []
    ];
}

/**
 * ALGORITHM STEP 8: Return Results
 */
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Recommendations generated successfully',
    'analysis_period' => [
        'days_tracked' => $data['days_tracked'],
        'date_range' => '7 days'
    ],
    'current_averages' => [
        'breakfast' => round($data['avg_breakfast']),
        'lunch' => round($data['avg_lunch']),
        'dinner' => round($data['avg_dinner']),
        'snacks' => round($data['avg_snacks']),
        'total' => round($data['avg_total']),
        'daily_goal' => round($data['avg_daily_goal'])
    ],
    'category_analysis' => $category_analysis,
    'recommendations' => $recommendations,
    'algorithm_info' => [
        'name' => 'Category-Based Calorie Analysis with Weighted Scoring',
        'version' => '1.0',
        'description' => 'Analyzes eating patterns by meal category and provides personalized recommendations based on nutritional science principles'
    ]
]);

$stmt->close();
$conn->close();
?>
