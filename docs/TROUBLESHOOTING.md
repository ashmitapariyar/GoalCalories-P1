# 🔧 GoalCalories - Troubleshooting Guide

> Solutions to common issues and debugging tips

---

## Table of Contents

1. [Setup Issues](#setup-issues)
2. [Database Problems](#database-problems)
3. [Authentication Errors](#authentication-errors)
4. [API Errors](#api-errors)
5. [Frontend Issues](#frontend-issues)
6. [Chart Problems](#chart-problems)
7. [Recommendations Not Working](#recommendations-not-working)
8. [Performance Issues](#performance-issues)
9. [Debugging Tips](#debugging-tips)

---

## 🛠️ Setup Issues

### Issue: XAMPP Won't Start

**Symptoms:**
- Apache shows "Busy" or "Not Started"
- MySQL won't start
- Red status indicators in XAMPP

**Solutions:**

#### Apache Port Conflict

**Problem:** Port 80 already in use

**Solution 1: Change Apache Port**
```apache
# Edit: C:\xampp\apache\conf\httpd.conf
# Find line:
Listen 80

# Change to:
Listen 8080

# Also change:
ServerName localhost:80
# To:
ServerName localhost:8080

# Restart Apache
# Access via: http://localhost:8080/
```

**Solution 2: Stop Conflicting Service**
```powershell
# Check what's using port 80
netstat -ano | findstr :80

# Common culprits:
# - IIS (Internet Information Services)
# - Skype
# - Other web servers

# Stop IIS:
net stop was /y
net stop w3svc
```

#### MySQL Port Conflict

**Problem:** Port 3306 already in use

```ini
# Edit: C:\xampp\mysql\bin\my.ini
# Find:
port=3306

# Change to:
port=3307

# Update config/database.php:
define('DB_SERVER', 'localhost:3307');
```

#### Permission Issues (Windows)

```cmd
# Run XAMPP Control Panel as Administrator
# Right-click xampp-control.exe → Run as administrator
```

---

### Issue: Project Files Not Found

**Symptoms:**
- "404 Not Found" when accessing pages
- "File does not exist" errors

**Solutions:**

1. **Verify Installation Path**
   ```
   Correct: C:\xampp\htdocs\GoalCalories-P1\
   Wrong:   C:\xampp\htdocs\GoalCalories-P1\GoalCalories-P1\
   ```

2. **Check File Permissions**
   ```powershell
   # Windows: Right-click folder → Properties → Security
   # Ensure "Users" have Read & Execute permissions
   ```

3. **Verify Apache Document Root**
   ```apache
   # C:\xampp\apache\conf\httpd.conf
   DocumentRoot "C:/xampp/htdocs"
   <Directory "C:/xampp/htdocs">
       # Should match
   </Directory>
   ```

---

## 💾 Database Problems

### Issue: Database Connection Failed

**Symptoms:**
```
Database connection failed: Connection refused
Database connection failed: Access denied
```

**Solutions:**

#### MySQL Not Running

```bash
# Check MySQL status in XAMPP Control Panel
# If stopped, click "Start" next to MySQL
```

#### Wrong Credentials

```php
// config/database.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // Default is empty
define('DB_NAME', 'goalcaloriep1');

// If you set a MySQL root password:
define('DB_PASSWORD', 'your_password');
```

#### Database Doesn't Exist

```sql
-- Run in phpMyAdmin SQL tab
CREATE DATABASE IF NOT EXISTS goalcaloriep1;
USE goalcaloriep1;
```

#### Test Connection

Create `test_db.php`:
```php
<?php
$conn = new mysqli('localhost', 'root', '', 'goalcaloriep1');

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Database connection successful!";
$conn->close();
?>
```

Navigate to: `http://localhost/GoalCalories-P1/test_db.php`

---

### Issue: Tables Not Found

**Symptoms:**
```
Table 'goalcaloriep1.register' doesn't exist
Table 'goalcaloriep1.daily_calories' doesn't exist
```

**Solutions:**

1. **Verify Database Selected**
   ```sql
   USE goalcaloriep1;
   SHOW TABLES;
   ```

2. **Re-create Tables**
   ```sql
   -- Run table creation SQL from SETUP_GUIDE.md
   -- Copy and paste each CREATE TABLE statement
   ```

3. **Check Table Names**
   ```sql
   -- Table names are case-sensitive on Linux
   -- Use exact names: register, daily_calories, food_calories
   ```

---

### Issue: Foreign Key Constraint Fails

**Symptoms:**
```
Cannot add or update a child row: a foreign key constraint fails
```

**Solutions:**

```sql
-- Check if user exists before inserting calories
SELECT id FROM register WHERE id = 1;

-- If user doesn't exist, register first
-- Or disable foreign key checks temporarily (not recommended)
SET FOREIGN_KEY_CHECKS=0;
-- Insert data
SET FOREIGN_KEY_CHECKS=1;
```

---

## 🔐 Authentication Errors

### Issue: Login Not Working

**Symptoms:**
- "Invalid email or password" even with correct credentials
- Redirects back to login page immediately
- No error message

**Solutions:**

#### 1. Verify User Exists

```sql
SELECT id, email, password FROM register WHERE email = 'your@email.com';
```

#### 2. Password Hash Issue

```php
// Test password verification
<?php
require 'config/database.php';

$email = 'test@example.com';
$password = 'password123';

$result = $conn->query("SELECT password FROM register WHERE email = '$email'");
$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    echo "✅ Password is correct";
} else {
    echo "❌ Password doesn't match";
}
?>
```

#### 3. Session Not Starting

```php
// Add to top of auth/login.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
var_dump($_SESSION);  // Check if session works
```

#### 4. Clear Browser Cookies

```javascript
// Browser Console (F12)
document.cookie.split(";").forEach(function(c) { 
    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
});
location.reload();
```

---

### Issue: Session Expires Immediately

**Symptoms:**
- Logged out after page refresh
- "User not logged in" errors
- Session data lost

**Solutions:**

#### 1. Check Session Configuration

```php
// php.ini
session.save_path = "C:/xampp/tmp"
session.gc_maxlifetime = 1800  // 30 minutes

// Verify session directory exists and is writable
```

#### 2. Ensure session_start() Called

```php
// Every PHP file that needs session
<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login
}
?>
```

#### 3. Cookie Settings

```php
// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => 'localhost',
    'secure' => false,  // Set true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
```

---

## 🔌 API Errors

### Issue: API Returns Empty Response

**Symptoms:**
- Blank page
- No JSON response
- Network error in console

**Solutions:**

#### 1. Check PHP Errors

```php
// Add to top of API file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Also check:
// C:\xampp\apache\logs\error.log
```

#### 2. Verify Content-Type Header

```php
// Should be in all API files
header('Content-Type: application/json');
```

#### 3. Check for Fatal Errors

```php
// Use try-catch
try {
    // API logic
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
```

---

### Issue: CORS Errors (Cross-Origin)

**Symptoms:**
```
Access to fetch at '...' from origin '...' has been blocked by CORS policy
```

**Solutions:**

```php
// Add to top of API files
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Or for specific domain:
header('Access-Control-Allow-Origin: http://localhost');
```

---

### Issue: JSON Parse Error

**Symptoms:**
```javascript
SyntaxError: Unexpected token < in JSON at position 0
```

**Cause:** PHP outputting HTML errors instead of JSON

**Solutions:**

```php
// Remove any echo/print before JSON output
// Remove HTML tags
// Check for whitespace before <?php

// Ensure only JSON is returned
ob_clean();  // Clear any previous output
echo json_encode($data);
exit;
```

---

## 🎨 Frontend Issues

### Issue: CSS Not Loading

**Symptoms:**
- Plain HTML with no styling
- Broken layout
- 404 errors for CSS files

**Solutions:**

#### 1. Verify File Paths

```html
<!-- dashboard.html -->
<!-- Correct (relative path): -->
<link rel="stylesheet" href="../assets/css/styles.css">

<!-- Check actual file location: -->
<!-- C:\xampp\htdocs\GoalCalories-P1\assets\css\styles.css -->
```

#### 2. Clear Browser Cache

```
Ctrl + Shift + Delete (Chrome/Edge/Firefox)
Or
Ctrl + F5 (Hard reload)
```

#### 3. Check File Permissions

```powershell
# Windows: Ensure "Read" permission for Everyone
```

---

### Issue: JavaScript Not Working

**Symptoms:**
- Buttons don't respond
- Forms don't submit
- Console shows errors

**Solutions:**

#### 1. Check Console for Errors

```
Press F12 → Console tab
Look for red error messages
```

#### 2. Common Errors

**Syntax Error:**
```javascript
// Wrong
function test() {
    console.log('test')  // Missing semicolon can cause issues
}

// Correct
function test() {
    console.log('test');
}
```

**Variable Not Defined:**
```javascript
// Ensure variables declared before use
let data = null;
// ... later
if (data) { // OK
```

**Function Not Found:**
```javascript
// Ensure function defined before calling
function myFunction() { }
myFunction();  // Call after definition
```

#### 3. Script Loading Order

```html
<!-- Load dependencies first -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Then your scripts -->
<script src="../assets/js/dashboard.js"></script>
```

---

## 📊 Chart Problems

### Issue: Charts Not Displaying

**Symptoms:**
- Empty canvas
- Blank chart area
- Console error: "Chart is not defined"

**Solutions:**

#### 1. Check Chart.js CDN

```html
<!-- Verify this line in dashboard.html -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Test in console: -->
<script>
console.log(typeof Chart);  // Should be "function", not "undefined"
</script>
```

#### 2. Check Internet Connection

```javascript
// Chart.js loads from CDN - requires internet
// Test:
fetch('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js')
    .then(() => console.log('✅ CDN accessible'))
    .catch(() => console.log('❌ No internet or CDN blocked'));
```

#### 3. Verify Canvas Elements

```html
<!-- Check HTML has canvas elements -->
<canvas id="calorieChart"></canvas>
<canvas id="mealDistributionChart"></canvas>
```

```javascript
// Check elements exist
console.log(document.getElementById('calorieChart'));  // Should not be null
```

#### 4. Check Data Format

```javascript
// Dashboard.js renderCharts() function
console.log('Chart data:', data);

// Data should have:
// - last7Days array
// - Each day should have date and calorie fields
```

---

### Issue: Charts Render But Show No Data

**Symptoms:**
- Empty chart with axes
- No lines or bars visible
- Labels present but no data points

**Solutions:**

```javascript
// Check data structure
const chartData = {
    labels: ['Jan 1', 'Jan 2'],  // Should not be empty
    datasets: [{
        data: [1800, 1900]  // Should not be empty
    }]
};

// Verify dates are being formatted correctly
console.log(last7Days.map(d => d.date));

// Check calories are numbers, not strings
console.log(typeof last7Days[0].totalCalories);  // Should be "number"
```

---

## 🥗 Recommendations Not Working

### Issue: No Recommendations Showing

**Symptoms:**
- Empty recommendations section
- "No recommendations" message
- Button does nothing

**Solutions:**

#### 1. Check Data Requirement

```javascript
// Need at least 1 day of tracked data
// Verify in database:
```

```sql
SELECT COUNT(*) FROM daily_calories WHERE user_id = 1;
-- Should return >= 1
```

#### 2. Verify API Response

```javascript
// Browser console
fetch('/api/get_food_recommendations.php', { credentials: 'include' })
    .then(r => r.json())
    .then(d => console.log(d));

// Check for:
// - success: true
// - recommendations array not empty
```

#### 3. Check Food Database

```sql
-- Verify foods exist
SELECT COUNT(*) FROM food_calories;
-- Should return 40

SELECT category, COUNT(*) FROM food_calories GROUP BY category;
-- Should show 10 per category
```

#### 4. Debug Algorithm

```javascript
// In get_food_recommendations.php
// Add debugging:
error_log("User averages: " . json_encode($averages));
error_log("Deficient categories: " . json_encode($deficient));
```

---

### Issue: Recommendations Are Not Personalized

**Symptoms:**
- Same recommendations every time
- Doesn't match eating pattern
- No priority order

**Solutions:**

#### 1. Verify 7-Day Data

```sql
-- Check user has varied data
SELECT 
    DATE(created_at) as date,
    breakfastCalories,
    lunchCalories,
    dinnerCalories,
    snackCalories
FROM daily_calories
WHERE user_id = 1
ORDER BY created_at DESC
LIMIT 7;

-- Should show different values
```

#### 2. Check Calculation Logic

```php
// In get_food_recommendations.php
// Verify averages being calculated correctly
var_dump($category_averages);
var_dump($ideal_distribution);
var_dump($gaps);
```

---

## 🐌 Performance Issues

### Issue: Slow Page Load

**Symptoms:**
- Pages take >5 seconds to load
- Timeout errors
- Laggy interface

**Solutions:**

#### 1. Check Database Indexes

```sql
-- Add indexes for better query performance
ALTER TABLE daily_calories ADD INDEX idx_user_date (user_id, created_at);
ALTER TABLE food_calories ADD INDEX idx_category (category);
```

#### 2. Optimize Queries

```php
// Bad - Multiple queries in loop
for ($i = 0; $i < 7; $i++) {
    $result = $conn->query("SELECT * FROM daily_calories WHERE ...");
}

// Good - Single query
$result = $conn->query("SELECT * FROM daily_calories WHERE ... LIMIT 7");
```

#### 3. Enable Caching

```php
// Cache recommendations for 1 hour
$cache_key = "recommendations_user_" . $user_id;
$cached = apcu_fetch($cache_key);

if ($cached === false) {
    $recommendations = generateRecommendations();
    apcu_store($cache_key, $recommendations, 3600);
} else {
    $recommendations = $cached;
}
```

#### 4. Minify Assets

```html
<!-- Use minified versions -->
<link rel="stylesheet" href="../assets/css/styles.min.css">
<script src="../assets/js/dashboard.min.js"></script>
```

---

## 🔍 Debugging Tips

### Enable Error Display (Development Only)

```php
// Add to top of index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/tmp/php_errors.log');
```

### Check Apache Error Log

```
Location: C:\xampp\apache\logs\error.log

# View last 50 lines:
Get-Content C:\xampp\apache\logs\error.log -Tail 50
```

### Check PHP Error Log

```
Location: C:\xampp\php\logs\php_error.log

# Or wherever php.ini sets error_log
```

### Browser DevTools

```
F12 → Open DevTools

Console Tab:
- JavaScript errors
- console.log() output
- API response data

Network Tab:
- API requests/responses
- Status codes
- Response time
- Request payload

Application Tab:
- Cookies
- Session storage
- Local storage
```

### MySQL Query Log

```sql
-- Enable query logging
SET GLOBAL general_log = 'ON';
SET GLOBAL general_log_file = 'C:/xampp/mysql/data/query.log';

-- Check logs
-- Location: C:\xampp\mysql\data\query.log

-- Disable when done
SET GLOBAL general_log = 'OFF';
```

### Debugging API Responses

```javascript
// Detailed fetch debugging
fetch('/api/dashboard.php', { credentials: 'include' })
    .then(response => {
        console.log('Status:', response.status);
        console.log('Headers:', response.headers);
        return response.text();  // Get raw response
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const json = JSON.parse(text);
            console.log('Parsed JSON:', json);
        } catch (e) {
            console.error('Not valid JSON:', e);
        }
    })
    .catch(error => console.error('Fetch error:', error));
```

### PHP Debugging

```php
// Variable inspection
var_dump($variable);
print_r($array);

// Stop execution and inspect
die(json_encode($data));

// Stack trace
debug_print_backtrace();

// Log to file
error_log("Debug: " . print_r($data, true));
```

---

## 🆘 Still Having Issues?

### Quick Diagnosis Checklist

- [ ] XAMPP Apache running?
- [ ] XAMPP MySQL running?
- [ ] Database `goalcaloriep1` exists?
- [ ] All 3 tables created?
- [ ] `config/database.php` credentials correct?
- [ ] Files in correct location?
- [ ] Browser console shows no errors?
- [ ] Session cookies enabled?
- [ ] Internet connection active (for Chart.js)?
- [ ] Logged in with valid session?
- [ ] At least 1 day of data tracked?

### Getting Help

1. **Check Documentation**
   - [USER_GUIDE.md](USER_GUIDE.md)
   - [SETUP_GUIDE.md](SETUP_GUIDE.md)
   - [API_REFERENCE.md](API_REFERENCE.md)
   - [ALGORITHM.md](ALGORITHM.md)

2. **Collect Information**
   - Exact error message
   - Browser console errors (F12)
   - Apache error log excerpt
   - Steps to reproduce
   - What you've already tried

3. **Create Issue** (if using GitHub)
   - Describe the problem
   - Include error messages
   - Specify environment (OS, PHP version, etc.)
   - Attach relevant logs

---

## 📋 Common Error Messages Reference

| Error | Likely Cause | Solution |
|-------|--------------|----------|
| "Database connection failed" | MySQL not running | Start MySQL in XAMPP |
| "User not logged in" | No session or expired | Re-login, check session config |
| "Table doesn't exist" | Tables not created | Run CREATE TABLE statements |
| "Undefined function" | PHP extension missing | Enable extension in php.ini |
| "Chart is not defined" | Chart.js not loaded | Check internet, verify CDN URL |
| "Cannot read property of undefined" | Variable not initialized | Check variable definition |
| "Access denied for user" | Wrong DB credentials | Update config/database.php |
| "Insufficient data" | Not enough tracked days | Track at least 1 day of meals |
| "404 Not Found" | Wrong file path | Verify file location and URL |
| "500 Internal Server Error" | PHP syntax/runtime error | Check Apache error log |

---

<div align="center">

**Need More Help?**

Check the [USER_GUIDE](USER_GUIDE.md) for usage instructions
or the [SETUP_GUIDE](SETUP_GUIDE.md) for installation help

[Back to Main README](../README.md) | [Setup Guide](SETUP_GUIDE.md) | [API Reference](API_REFERENCE.md)

Last Updated: January 5, 2026

</div>
