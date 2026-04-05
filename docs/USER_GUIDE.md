# 📖 GoalCalories - User Guide

> Your complete guide to tracking calories and getting personalized nutrition recommendations

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Registration & Login](#registration--login)
3. [Setting Up Your Profile](#setting-up-your-profile)
4. [Tracking Your Meals](#tracking-your-meals)
5. [Understanding Your Dashboard](#understanding-your-dashboard)
6. [Food Recommendations](#food-recommendations)
7. [Reports & Analytics](#reports--analytics)
8. [Tips for Best Results](#tips-for-best-results)
9. [FAQ](#faq)

---

## 🚀 Getting Started

### What is GoalCalories?

GoalCalories is a smart nutrition tracking app that helps you:
- ✅ Track daily calorie intake across meals
- ✅ Calculate your personalized calorie goals
- ✅ Get intelligent food recommendations
- ✅ Visualize your eating patterns with charts
- ✅ Monitor your progress over time

### First Time Setup (3 Minutes)

1. **Open the App**
   - Navigate to: `http://localhost/GoalCalories-P1/`
   - You'll see the home page

2. **Create Account**
   - Click "Register" or "Get Started"
   - Fill in your details
   - Click "Register"

3. **Login**
   - Enter your email and password
   - Click "Login"

4. **You're Ready!** 🎉

---

## 🔐 Registration & Login

### Creating Your Account

1. **Click "Register"** from the home page

2. **Fill in the Registration Form**
   - **Full Name**: Your complete name
   - **Phone Number**: Contact number (10 digits)
   - **Email**: Valid email address (used for login)
   - **Password**: Strong password (min 8 characters)
   - **Confirm Password**: Re-enter your password

3. **Submit**
   - Click "Register"
   - You'll be redirected to login page

### Logging In

1. **Enter Credentials**
   - Email address
   - Password

2. **Click "Login"**
   - You'll be taken to your dashboard

### Forgot Password?

Currently, contact your administrator to reset your password. (Feature coming soon!)

---

## 👤 Setting Up Your Profile

### Calculating Your Daily Calorie Goal

The first thing you should do is calculate your personalized calorie goal.

1. **Navigate to "Daily Calories"** section on dashboard

2. **Enter Your Information**
   - **Age**: Your current age in years
   - **Gender**: Male or Female
   - **Weight**: In kilograms (e.g., 70)
   - **Height**: In centimeters (e.g., 175)
   - **Activity Level**: Choose one:
     - **Sedentary**: Little or no exercise
     - **Lightly Active**: Exercise 1-3 days/week
     - **Moderately Active**: Exercise 3-5 days/week
     - **Very Active**: Exercise 6-7 days/week
     - **Extra Active**: Very hard exercise/physical job

3. **Click "Calculate"**
   - Your daily calorie goal will be displayed
   - This uses the Mifflin-St Jeor equation for accuracy

### Example Calculation

**Profile:** 25-year-old male, 70kg, 175cm, moderately active
- **BMR**: 1,687 calories
- **Daily Goal**: 1,687 × 1.55 = **2,615 calories**

---

## 🍽️ Tracking Your Meals

### How to Log Daily Food Intake

1. **Go to "Daily Calories" Section**

2. **Enter Calories for Each Meal**
   - **Breakfast**: Morning meal (e.g., 400 calories)
   - **Lunch**: Midday meal (e.g., 650 calories)
   - **Snacks**: Between-meal snacks (e.g., 200 calories)
   - **Dinner**: Evening meal (e.g., 550 calories)

3. **Set Your Daily Goal**
   - Enter the goal calculated earlier
   - Or enter a custom goal

4. **Click "Store Data"**
   - Total calories calculated automatically
   - Surplus/Deficit computed
   - Data saved to your history

### Important Notes

- 📌 **One Entry Per Day**: You can only save one record per day
- 🔄 **Update Anytime**: Clicking "Store Data" again will update today's entry
- ➕ **Auto-Calculation**: Total calories = Breakfast + Lunch + Snacks + Dinner
- 📊 **Instant Feedback**: See if you're in surplus or deficit immediately

### Example Daily Entry

```
Breakfast:    450 calories  🥑 Avocado toast + eggs
Lunch:        650 calories  🥗 Chicken salad
Snacks:       200 calories  🍎 Apple + almonds
Dinner:       550 calories  🍗 Grilled chicken + veggies
Daily Goal:   2000 calories

Total:        1850 calories
Status:       150 calorie deficit ✓
```

---

## 📊 Understanding Your Dashboard

### Dashboard Sections

#### 1. **Statistics Cards** (Top Section)
- **Days Tracked**: Total days you've logged meals
- **Current Streak**: Consecutive days of tracking
- **Avg Daily Intake**: Your average calories per day
- **Goal Achievement**: Percentage of days you met your goal

#### 2. **Daily Calories Calculator** (Left)
- Input your profile info
- Calculate BMR and daily goal
- Store today's meal data

#### 3. **7-Day Analysis** (Middle)
- View your last 7 days of data
- See moving average trend
- Track surplus/deficit patterns

#### 4. **Reports** (Right)
- Generate detailed reports
- View interactive charts
- Export data (coming soon)

#### 5. **Food Recommendations** (Bottom)
- Get personalized suggestions
- Identify nutritional gaps
- Discover foods to balance diet

### Status Indicators

| Icon | Status | Meaning |
|------|--------|---------|
| 🟢 | Balanced | Within 50 calories of goal |
| 🟡 | Deficit | Below goal by >50 calories |
| 🔴 | Surplus | Above goal by >50 calories |

---

## 🥗 Food Recommendations

### What Are Food Recommendations?

Our smart algorithm analyzes your eating patterns and suggests foods to improve your nutrition balance.

### How It Works

1. **Analyzes Your Data**
   - Reviews your last 7 days of meals
   - Calculates average calories per meal category
   - Compares to ideal distribution

2. **Identifies Gaps**
   - Breakfast should be ~27.5% of daily calories
   - Lunch should be ~32.5%
   - Dinner should be ~27.5%
   - Snacks should be ~12.5%

3. **Prioritizes Recommendations**
   - Ranks by severity (High → Medium → Low)
   - Suggests specific foods that fit your gaps

### Getting Your Recommendations

1. **Track at least 1 day** of meals (7 days recommended)

2. **Click "Food Recommendations"** button

3. **Review Your Results**
   - **Category Analysis**: See which meals need attention
   - **Severity Levels**: 
     - 🔴 High: Urgent (>30% deviation)
     - 🟡 Medium: Should improve (15-30%)
     - 🟢 Low: Minor (<15%)
   - **Food Suggestions**: 3 specific foods per category

### Example Recommendation

```
📊 BREAKFAST ANALYSIS
Current Average: 150 calories
Recommended:     550 calories
Gap:             400 calories
Severity:        🔴 HIGH

📋 SUGGESTED FOODS:
1. Avocado toast with egg         (380 cal)
   🥑 Healthy fats, protein, filling

2. Scrambled eggs + whole wheat   (350 cal)
   🍳 High protein, sustained energy

3. Banana peanut butter smoothie  (320 cal)
   🍌 Quick prep, portable
```

### Understanding Recommendations

- **Deficient**: You need MORE calories in this category
- **Excess**: You're eating TOO MUCH in this category
- **Balanced**: Perfect! Keep it up 🎉

---

## 📈 Reports & Analytics

### Generating Reports

1. **Click "Reports"** section

2. **Click "Display Report"** button

3. **View Your Data**
   - Table with daily breakdown
   - Interactive charts
   - Summary statistics

### Chart Types

#### 1. **Daily Calorie Trends** (Line Chart)
- Blue line: Your actual intake
- Red dashed line: Your daily goal
- Shows patterns over last 7 days

**What to Look For:**
- Consistency: Lines should be relatively stable
- Goal alignment: Blue line near red line
- Trends: Improving or declining?

#### 2. **Meal Distribution** (Doughnut Chart)
- Shows percentage breakdown by meal
- Compare to ideal distribution

**Ideal Distribution:**
- 🌅 Breakfast: 27.5%
- 🌞 Lunch: 32.5%
- 🌙 Dinner: 27.5%
- 🍿 Snacks: 12.5%

### Interpreting Your Charts

```
✅ GOOD PATTERN:
- Consistent daily intake
- Close to goal line
- Balanced meal distribution

⚠️ NEEDS WORK:
- High variability (peaks and valleys)
- Far from goal line
- One meal dominates others
```

---

## 💡 Tips for Best Results

### 1. **Track Consistently**
- Log meals daily for accurate analysis
- Try to track at the same time each day
- Don't skip meals - log everything!

### 2. **Be Honest with Portions**
- Accurate calories = better recommendations
- Use measuring tools when possible
- Include cooking oils and condiments

### 3. **Follow Recommendations Gradually**
- Don't change everything at once
- Start with ONE category improvement
- Give your body time to adjust

### 4. **Use the 7-Day Analysis**
- Check weekly trends, not daily fluctuations
- One bad day doesn't ruin progress
- Focus on overall patterns

### 5. **Experiment with Suggested Foods**
- Try at least 1-2 recommended foods per week
- Find what you enjoy and works for you
- Build a sustainable eating pattern

### 6. **Set Realistic Goals**
- Aim for 80% adherence, not perfection
- Celebrate small wins
- Progress > Perfection

---

## ❓ FAQ

### General Questions

**Q: How often should I track?**
A: Daily tracking gives best results, but even 5 days/week is valuable.

**Q: Can I update today's entry?**
A: Yes! Just enter new values and click "Store Data" again.

**Q: How is my daily goal calculated?**
A: Using the Mifflin-St Jeor equation based on age, gender, weight, height, and activity level.

### Recommendations

**Q: Why aren't I seeing recommendations?**
A: You need at least 1 day of tracked meals. For best results, track 7 days.

**Q: Why is breakfast marked as "deficient"?**
A: You're eating significantly less at breakfast than the optimal 27.5% of daily calories.

**Q: Can I ignore recommendations?**
A: Yes, they're suggestions! Use what makes sense for your lifestyle.

**Q: What if I don't like the suggested foods?**
A: The algorithm suggests foods with similar calories. Look for alternatives in the same calorie range.

### Charts & Reports

**Q: Charts not showing?**
A: Make sure you have at least 1 day of data. Check your internet connection (Chart.js needs to load).

**Q: What's a "good" chart pattern?**
A: Consistent intake close to your goal line with balanced meal distribution.

**Q: Can I export my data?**
A: Coming soon! Currently, you can screenshot reports.

### Technical

**Q: Is my data secure?**
A: Yes! Passwords are hashed, and we use secure database connections.

**Q: Can I access from mobile?**
A: Yes! The app is fully responsive and works on all devices.

**Q: What browsers are supported?**
A: Chrome, Firefox, Safari, Edge (modern versions).

---

## 🎯 Success Story Example

**Sarah's Journey**

**Week 1:**
- Started tracking daily
- Goal: 2000 calories
- Averaging: 1500 calories
- Skipping breakfast most days

**After Seeing Recommendations:**
- Added morning smoothie (350 cal)
- Increased to 1850 average
- Better energy throughout day

**Week 4:**
- Consistently hitting 1900-2000 calories
- Balanced meals across all categories
- Feeling great! 🎉

---

## 🆘 Need Help?

### If You're Stuck

1. **Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)**
2. **Review this guide again**
3. **Contact support** (if available)

### Common Issues

- **Login problems**: Clear browser cache, check password
- **Data not saving**: Check internet connection, verify session
- **Recommendations not loading**: Track more days, refresh page

---

## 🎓 Next Steps

Now that you know how to use GoalCalories:

1. ✅ Track meals daily for 1 week
2. ✅ Generate your first report
3. ✅ Get food recommendations
4. ✅ Try 1-2 suggested foods
5. ✅ Check progress after 2 weeks

**Remember**: Consistency beats perfection! 🌟

---

<div align="center">

**Happy Tracking! 🎯**

[Back to Main README](../README.md) | [Setup Guide](SETUP_GUIDE.md) | [Algorithm Details](ALGORITHM.md)

</div>
