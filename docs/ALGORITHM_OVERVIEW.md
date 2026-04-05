# Algorithm Overview - GoalCalories System

## Simple Explanation of Both Algorithms

This document provides a brief, easy-to-understand overview of the two main algorithms used in the GoalCalories application.

---

## 1. 7-Day Moving Average Analysis Algorithm

### Purpose
To track if a user is eating the right amount of calories by looking at their last 7 days of eating habits.

### How It Works (Step-by-Step)

**Step 1: Collect Data**
- The system looks at the last 7 days of the user's meal tracking
- It collects how many calories they ate each day

**Step 2: Calculate Average**
- Adds up all the calories from those 7 days
- Divides by the number of days tracked to get the daily average

**Step 3: Compare with Goal**
- Takes the user's personal daily calorie goal (e.g., 2200 calories)
- Compares the 7-day average with this goal

**Step 4: Provide Status**
- **Over Intake**: If average is higher than goal → eating too much
- **Under Intake**: If average is lower than goal → eating too little
- **Balanced Intake**: If average matches goal → perfect balance

**Step 5: Give Recommendation**
- Tells the user how many calories they need to add or reduce
- Provides simple advice (e.g., "Add 200 calories" or "Reduce 150 calories")

### Example
```
User's Goal: 2200 calories/day
Last 7 days average: 1950 calories/day
Result: "Under Intake - Add 250 calories more per day"
```

### Why It's Useful
- Moving averages smooth out daily fluctuations (like weekend overeating)
- Shows overall eating trends, not just one bad or good day
- Easy to understand: one simple number tells the whole story

---

## 2. Category-Based Calorie Analysis with Weighted Scoring (CBCAWS)

### Purpose
To analyze eating patterns by meal type (breakfast, lunch, dinner, snacks) and recommend specific foods to improve nutrition balance.

### How It Works (Step-by-Step)

**Step 1: Collect Category Data**
- Looks at the last 7 days of meals
- Calculates average calories for each meal category:
  - Breakfast average
  - Lunch average
  - Dinner average
  - Snacks average

**Step 2: Know the Ideal Distribution**
- Based on nutritional science, calories should be distributed as:
  - **Breakfast**: 25-30% (about 550-660 cal for 2200 goal)
  - **Lunch**: 30-35% (about 660-770 cal for 2200 goal)
  - **Dinner**: 25-30% (about 550-660 cal for 2200 goal)
  - **Snacks**: 10-15% (about 220-330 cal for 2200 goal)

**Step 3: Compare Current vs. Ideal**
- For each meal category, calculates:
  - What percentage of calories user currently eats
  - What percentage they should ideally eat
  - The gap between current and ideal

**Step 4: Calculate Severity Scores**
- Gives each deficient category a "severity score" (0-100)
- Higher score = more urgent to fix
- Example: If eating only 180 cal breakfast (should be 550) = high severity

**Step 5: Prioritize Recommendations**
- Sorts categories by severity (worst first)
- Focuses on the most problematic eating patterns
- Example: "Breakfast is your biggest issue, then snacks"

**Step 6: Match Foods from Database**
- Looks in the food database for the deficient category
- Finds foods that match the calorie gap needed
- Selects 3 best food suggestions

**Step 7: Generate Personalized Advice**
- Creates specific messages like:
  - "Your breakfast intake is low (180 cal vs. recommended 562 cal)"
  - "Try adding: Muffin, Cereal, or Scone"
  - Shows calorie gap: "+382 cal needed"

### Example
```
User's breakfast average: 180 cal
Ideal breakfast: 550 cal
Gap: 370 cal (HIGH PRIORITY)

Recommendation:
"Your breakfast intake is low. Add 370 calories."
Suggested foods:
- Muffin (385 cal/100g)
- Cereal (387 cal/100g)
- Scone (364 cal/100g)
```

### Why It's Useful
- **Specific**: Doesn't just say "eat more" - tells you WHAT to eat
- **Prioritized**: Fixes the worst problems first
- **Scientific**: Based on nutritional distribution guidelines
- **Actionable**: Provides real food options from database
- **Personalized**: Calculates exact calorie needs for each person

---

## Key Differences Between the Two Algorithms

| Feature | 7-Day Analysis | CBCAWS (Recommendations) |
|---------|---------------|-------------------------|
| **Focus** | Overall calorie intake | Meal category distribution |
| **Output** | Single status (Over/Under/Balanced) | Multiple specific recommendations |
| **Granularity** | Total daily calories | Breakfast, lunch, dinner, snacks |
| **Advice Type** | General (add/reduce calories) | Specific (exact foods to eat) |
| **Complexity** | Simple average calculation | Multi-step weighted analysis |
| **Best For** | Quick health check | Detailed nutrition improvement |

---

## Real-World Scenario

### Scenario: User "Anjita Nepal"

**7-Day Analysis Says:**
- Average intake: 2130 calories/day
- Goal: 2200 calories/day
- Status: **Under Intake by 70 calories**
- Simple advice: "Add 70 more calories per day"

**CBCAWS Algorithm Digs Deeper:**
- Breakfast: 220 cal (should be 605 cal) → **DEFICIENT by 385 cal**
- Lunch: 800 cal (should be 715 cal) → Excess by 85 cal
- Dinner: 840 cal (should be 605 cal) → Excess by 235 cal
- Snacks: 270 cal (should be 275 cal) → Balanced

**Key Insight:**
The 7-day analysis shows she's slightly under her goal, but doesn't explain WHY. The CBCAWS algorithm reveals the real problem: she's skipping breakfast and compensating at dinner. It recommends:
1. Increase breakfast (HIGH PRIORITY)
2. Reduce dinner slightly
3. This will improve metabolism and energy distribution

---

## Summary

### 7-Day Moving Average Analysis
**In One Sentence:** "Are you eating enough calories overall?"

**Simple Analogy:** Like checking your bank account balance - are you spending too much or too little?

### CBCAWS (Category-Based Analysis)
**In One Sentence:** "Are you eating the right foods at the right times?"

**Simple Analogy:** Like a financial advisor breaking down your spending by category (rent, food, entertainment) and suggesting where to adjust.

---

## Technical Terms Simplified

| Technical Term | Simple Explanation |
|---------------|-------------------|
| **Moving Average** | Average calculated over a sliding time window (last 7 days) |
| **Weighted Scoring** | Giving more importance to bigger problems |
| **Distribution** | How calories are spread across different meals |
| **Severity Score** | Number that shows how urgent a problem is |
| **Deficit** | Not eating enough in a category |
| **Threshold** | Minimum difference needed to trigger action (e.g., 50 calories) |

---

## Benefits for Users

### 7-Day Analysis Benefits:
✅ Quick health snapshot  
✅ Easy to understand  
✅ Tracks progress over time  
✅ Smooths out daily variations  

### CBCAWS Benefits:
✅ Specific actionable advice  
✅ Identifies hidden eating problems  
✅ Provides exact food suggestions  
✅ Science-based recommendations  
✅ Prioritizes most important changes  

---

## Conclusion

Both algorithms work together to provide comprehensive nutrition guidance:

1. **7-Day Analysis** tells you if you're on track overall
2. **CBCAWS** tells you HOW to improve your eating patterns

Think of it like a car dashboard:
- 7-Day Analysis = Speedometer (overall speed/progress)
- CBCAWS = GPS Navigation (specific directions to improve)

Together, they help users not just track calories, but actually improve their eating habits with scientific, personalized recommendations.
