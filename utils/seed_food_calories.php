<?php
/**
 * Seed Food Calories Database
 * Populates the food_calories table with sample data
 */

require_once '../config/database.php';

try {
    $conn = getDatabaseConnection();
    
    // Check if table exists
    $check_table = "SHOW TABLES LIKE 'food_calories'";
    $result = $conn->query($check_table);
    
    if ($result->num_rows === 0) {
        die("ERROR: food_calories table does not exist. Please create it first using:\n\n" .
            "CREATE TABLE food_calories (\n" .
            "    food_id INT PRIMARY KEY,\n" .
            "    food_name VARCHAR(100) NOT NULL,\n" .
            "    calories_per_100g INT NOT NULL,\n" .
            "    category VARCHAR(20) CHECK (category IN ('Breakfast', 'Lunch', 'Snacks', 'Dinner'))\n" .
            ");");
    }
    
    // Clear existing data (optional)
    $conn->query("DELETE FROM food_calories");
    echo "✓ Cleared existing data<br><br>";
    
    // Prepare insert statement
    $insert_sql = "INSERT INTO food_calories (food_id, food_name, calories_per_100g, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    
    // Food data array
    $foods = [
        // Breakfast (10 foods)
        [1, 'Oatmeal with berries', 300, 'Breakfast'],
        [2, 'Greek yogurt parfait', 250, 'Breakfast'],
        [3, 'Scrambled eggs with toast', 350, 'Breakfast'],
        [4, 'Banana peanut butter smoothie', 320, 'Breakfast'],
        [5, 'Avocado toast with egg', 380, 'Breakfast'],
        [6, 'Whole grain cereal with milk', 280, 'Breakfast'],
        [7, 'French toast with syrup', 310, 'Breakfast'],
        [8, 'Protein pancakes', 290, 'Breakfast'],
        [9, 'Vegetable omelette', 260, 'Breakfast'],
        [10, 'Granola with yogurt', 340, 'Breakfast'],
        
        // Lunch (10 foods)
        [11, 'Grilled chicken salad', 400, 'Lunch'],
        [12, 'Quinoa bowl with vegetables', 450, 'Lunch'],
        [13, 'Turkey sandwich on whole grain', 420, 'Lunch'],
        [14, 'Lentil soup with bread', 380, 'Lunch'],
        [15, 'Salmon with brown rice', 480, 'Lunch'],
        [16, 'Veggie wrap with hummus', 350, 'Lunch'],
        [17, 'Chicken Caesar wrap', 410, 'Lunch'],
        [18, 'Mediterranean pasta', 430, 'Lunch'],
        [19, 'Tuna salad sandwich', 390, 'Lunch'],
        [20, 'Burrito bowl', 460, 'Lunch'],
        
        // Dinner (10 foods)
        [21, 'Baked chicken with sweet potato', 420, 'Dinner'],
        [22, 'Stir-fry vegetables with tofu', 380, 'Dinner'],
        [23, 'Grilled fish with broccoli', 350, 'Dinner'],
        [24, 'Turkey meatballs with zucchini noodles', 400, 'Dinner'],
        [25, 'Vegetable curry with chickpeas', 390, 'Dinner'],
        [26, 'Grilled shrimp with quinoa', 370, 'Dinner'],
        [27, 'Beef stir-fry with vegetables', 440, 'Dinner'],
        [28, 'Baked salmon with asparagus', 410, 'Dinner'],
        [29, 'Chicken fajitas', 430, 'Dinner'],
        [30, 'Vegetarian lasagna', 450, 'Dinner'],
        
        // Snacks (10 foods)
        [31, 'Apple slices with almond butter', 180, 'Snacks'],
        [32, 'Handful of mixed nuts', 160, 'Snacks'],
        [33, 'Protein bar', 200, 'Snacks'],
        [34, 'Carrot sticks with hummus', 120, 'Snacks'],
        [35, 'Greek yogurt', 150, 'Snacks'],
        [36, 'Hard-boiled eggs (2)', 140, 'Snacks'],
        [37, 'Rice cakes with peanut butter', 170, 'Snacks'],
        [38, 'Trail mix', 190, 'Snacks'],
        [39, 'Cheese and crackers', 210, 'Snacks'],
        [40, 'Fruit smoothie', 130, 'Snacks']
    ];
    
    // Insert foods
    $inserted = 0;
    foreach ($foods as $food) {
        $stmt->bind_param("isis", $food[0], $food[1], $food[2], $food[3]);
        if ($stmt->execute()) {
            $inserted++;
        }
    }
    
    echo "✓ Inserted $inserted foods successfully!<br><br>";
    
    // Show summary
    $summary_sql = "SELECT category, COUNT(*) as count FROM food_calories GROUP BY category";
    $summary_result = $conn->query($summary_sql);
    
    echo "<h3>Food Database Summary:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Category</th><th>Number of Foods</th></tr>";
    
    while ($row = $summary_result->fetch_assoc()) {
        echo "<tr><td>{$row['category']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table><br>";
    
    // Show sample foods
    echo "<h3>Sample Foods:</h3>";
    $sample_sql = "SELECT * FROM food_calories ORDER BY category, food_id LIMIT 15";
    $sample_result = $conn->query($sample_sql);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Food Name</th><th>Calories/100g</th><th>Category</th></tr>";
    
    while ($row = $sample_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['food_id']}</td>";
        echo "<td>{$row['food_name']}</td>";
        echo "<td>{$row['calories_per_100g']}</td>";
        echo "<td>{$row['category']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    echo "<h3 style='color: green;'>✓ Food database seeded successfully!</h3>";
    echo "<p><a href='../pages/dashboard.html'>Go to Dashboard</a> to test recommendations</p>";
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
