-- Seed Data for food_calories Table
-- Run this in phpMyAdmin or MySQL to populate your food database

USE goalcaloriep1;

-- Clear existing data (optional)
-- DELETE FROM food_calories;

-- Insert Breakfast Foods
INSERT INTO food_calories (food_id, food_name, calories_per_100g, category) VALUES
(1, 'Oatmeal with berries', 300, 'Breakfast'),
(2, 'Greek yogurt parfait', 250, 'Breakfast'),
(3, 'Scrambled eggs with toast', 350, 'Breakfast'),
(4, 'Banana peanut butter smoothie', 320, 'Breakfast'),
(5, 'Avocado toast with egg', 380, 'Breakfast'),
(6, 'Whole grain cereal with milk', 280, 'Breakfast'),
(7, 'French toast with syrup', 310, 'Breakfast'),
(8, 'Protein pancakes', 290, 'Breakfast'),
(9, 'Vegetable omelette', 260, 'Breakfast'),
(10, 'Granola with yogurt', 340, 'Breakfast');

-- Insert Lunch Foods
INSERT INTO food_calories (food_id, food_name, calories_per_100g, category) VALUES
(11, 'Grilled chicken salad', 400, 'Lunch'),
(12, 'Quinoa bowl with vegetables', 450, 'Lunch'),
(13, 'Turkey sandwich on whole grain', 420, 'Lunch'),
(14, 'Lentil soup with bread', 380, 'Lunch'),
(15, 'Salmon with brown rice', 480, 'Lunch'),
(16, 'Veggie wrap with hummus', 350, 'Lunch'),
(17, 'Chicken Caesar wrap', 410, 'Lunch'),
(18, 'Mediterranean pasta', 430, 'Lunch'),
(19, 'Tuna salad sandwich', 390, 'Lunch'),
(20, 'Burrito bowl', 460, 'Lunch');

-- Insert Dinner Foods
INSERT INTO food_calories (food_id, food_name, calories_per_100g, category) VALUES
(21, 'Baked chicken with sweet potato', 420, 'Dinner'),
(22, 'Stir-fry vegetables with tofu', 380, 'Dinner'),
(23, 'Grilled fish with broccoli', 350, 'Dinner'),
(24, 'Turkey meatballs with zucchini noodles', 400, 'Dinner'),
(25, 'Vegetable curry with chickpeas', 390, 'Dinner'),
(26, 'Grilled shrimp with quinoa', 370, 'Dinner'),
(27, 'Beef stir-fry with vegetables', 440, 'Dinner'),
(28, 'Baked salmon with asparagus', 410, 'Dinner'),
(29, 'Chicken fajitas', 430, 'Dinner'),
(30, 'Vegetarian lasagna', 450, 'Dinner');

-- Insert Snacks Foods
INSERT INTO food_calories (food_id, food_name, calories_per_100g, category) VALUES
(31, 'Apple slices with almond butter', 180, 'Snacks'),
(32, 'Handful of mixed nuts', 160, 'Snacks'),
(33, 'Protein bar', 200, 'Snacks'),
(34, 'Carrot sticks with hummus', 120, 'Snacks'),
(35, 'Greek yogurt', 150, 'Snacks'),
(36, 'Hard-boiled eggs (2)', 140, 'Snacks'),
(37, 'Rice cakes with peanut butter', 170, 'Snacks'),
(38, 'Trail mix', 190, 'Snacks'),
(39, 'Cheese and crackers', 210, 'Snacks'),
(40, 'Fruit smoothie', 130, 'Snacks');

-- Verify the data
SELECT category, COUNT(*) as food_count 
FROM food_calories 
GROUP BY category;

-- Show sample foods
SELECT * FROM food_calories LIMIT 10;
