# 🔧 GoalCalories - Complete Setup Guide

> Step-by-step installation and configuration instructions

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installing Prerequisites](#installing-prerequisites)
3. [Project Installation](#project-installation)
4. [Database Setup](#database-setup)
5. [Configuration](#configuration)
6. [Seeding Sample Data](#seeding-sample-data)
7. [Verification](#verification)
8. [Troubleshooting Setup](#troubleshooting-setup)

---

## 💻 System Requirements

### Minimum Requirements

| Component | Requirement |
|-----------|-------------|
| **Operating System** | Windows 10/11, macOS 10.14+, Ubuntu 18.04+ |
| **PHP Version** | 7.4 or higher |
| **MySQL Version** | 5.7 or higher |
| **Web Server** | Apache 2.4+ |
| **RAM** | 2 GB minimum |
| **Storage** | 500 MB free space |
| **Browser** | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |

### Recommended Requirements

- **PHP**: 8.0+
- **MySQL**: 8.0+
- **RAM**: 4 GB
- **Storage**: 1 GB free space

---

## 📥 Installing Prerequisites

### Option 1: XAMPP (Recommended - All-in-One)

#### Windows

1. **Download XAMPP**
   - Go to: https://www.apachefriends.org/
   - Download latest version (PHP 7.4+)

2. **Install XAMPP**
   - Run the installer
   - Choose installation directory: `C:\xampp`
   - Select components:
     - ✅ Apache
     - ✅ MySQL
     - ✅ PHP
     - ✅ phpMyAdmin

3. **Start XAMPP Control Panel**
   - Run `C:\xampp\xampp-control.exe`
   - Start Apache module
   - Start MySQL module

#### macOS

1. **Download XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download macOS version

2. **Install**
   - Open downloaded DMG file
   - Drag XAMPP to Applications
   - Run XAMPP from Applications

3. **Start Services**
   - Open XAMPP Manager
   - Start Apache and MySQL

#### Linux (Ubuntu/Debian)

```bash
# Download installer
wget https://www.apachefriends.org/xampp-files/8.0.x/xampp-linux-x64-8.0.x-installer.run

# Make executable
chmod +x xampp-linux-x64-8.0.x-installer.run

# Install
sudo ./xampp-linux-x64-8.0.x-installer.run

# Start services
sudo /opt/lampp/lampp start
```

### Option 2: Individual Components

#### Installing PHP (Windows)

1. Download from: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH environment variable
4. Verify: `php -v`

#### Installing MySQL (Windows)

1. Download from: https://dev.mysql.com/downloads/installer/
2. Run MySQL Installer
3. Choose "Server only" installation
4. Set root password
5. Verify: `mysql --version`

#### Installing Apache (Windows)

1. Download from: https://httpd.apache.org/download.cgi
2. Extract to `C:\Apache24`
3. Install as service: `httpd.exe -k install`
4. Start service: `httpd.exe -k start`

---

## 📦 Project Installation

### Method 1: Clone from Repository

```bash
# Navigate to web server directory
cd C:\xampp\htdocs

# Clone the repository
git clone https://github.com/yourusername/GoalCalories-P1.git

# Or if you have the ZIP file:
# Unzip to C:\xampp\htdocs\GoalCalories-P1
```

### Method 2: Manual Download

1. Download project ZIP file
2. Extract contents
3. Copy `GoalCalories-P1` folder to:
   - **Windows**: `C:\xampp\htdocs\`
   - **macOS**: `/Applications/XAMPP/htdocs/`
   - **Linux**: `/opt/lampp/htdocs/`

### Verify File Structure

Your directory should look like this:

```
C:\xampp\htdocs\GoalCalories-P1\
├── index.php
├── README.md
├── api/
├── auth/
├── config/
├── pages/
├── assets/
├── utils/
└── docs/
```

---

## 🗄️ Database Setup

### Step 1: Access phpMyAdmin

1. **Open Browser**
   - Navigate to: `http://localhost/phpmyadmin`

2. **Login**
   - Username: `root`
   - Password: (leave blank, or use your set password)

### Step 2: Create Database

#### Option A: Using phpMyAdmin Interface

1. Click "New" in left sidebar
2. Database name: `goalcaloriep1`
3. Collation: `utf8mb4_general_ci`
4. Click "Create"

#### Option B: Using SQL

1. Click "SQL" tab
2. Paste and execute:

```sql
CREATE DATABASE goalcaloriep1 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;
```

### Step 3: Create Tables

1. Select `goalcaloriep1` database from left sidebar
2. Click "SQL" tab
3. Copy and execute the following schema:

#### Table 1: Users (register)

```sql
CREATE TABLE register (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    phonenumber VARCHAR(15) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirmpassword VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table 2: Daily Calories Tracking

```sql
CREATE TABLE daily_calories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    breakfastCalories INT DEFAULT 0,
    lunchCalories INT DEFAULT 0,
    snackCalories INT DEFAULT 0,
    dinnerCalories INT DEFAULT 0,
    totalCalories INT DEFAULT 0,
    dailyCalories INT DEFAULT 0,
    surplus INT DEFAULT 0,
    deficit INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES register(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, created_at),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table 3: Food Database

```sql
CREATE TABLE food_calories (
    food_id INT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    calories_per_100g INT NOT NULL,
    category VARCHAR(20) CHECK (category IN ('Breakfast', 'Lunch', 'Snacks', 'Dinner')),
    protein VARCHAR(10),
    benefits TEXT,
    INDEX idx_category (category),
    INDEX idx_calories (calories_per_100g)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 4: Verify Tables

Run this query to confirm all tables were created:

```sql
SHOW TABLES FROM goalcaloriep1;
```

Expected output:
- `register`
- `daily_calories`
- `food_calories`

---

## ⚙️ Configuration

### Database Configuration

1. **Open Configuration File**
   ```
   C:\xampp\htdocs\GoalCalories-P1\config\database.php
   ```

2. **Update Credentials** (if needed)

```php
<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // Change if you set a MySQL password
define('DB_NAME', 'goalcaloriep1');

// Attempt to connect to MySQL database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

3. **Save the File**

### PHP Configuration (Optional)

If you encounter issues, configure PHP settings:

1. **Locate php.ini**
   - Windows: `C:\xampp\php\php.ini`
   - macOS: `/Applications/XAMPP/xamppfiles/etc/php.ini`
   - Linux: `/opt/lampp/etc/php.ini`

2. **Enable Extensions** (remove `;` to uncomment)
   ```ini
   extension=mysqli
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   ```

3. **Set Timezone**
   ```ini
   date.timezone = Asia/Kolkata  ; Or your timezone
   ```

4. **Restart Apache** after changes

---

## 🌱 Seeding Sample Data

### Option 1: Using Web Interface (Easiest)

#### Seed User Data

1. Navigate to: `http://localhost/GoalCalories-P1/utils/seed_data.php`
2. Page will execute and show success message
3. Creates test user: `test@example.com` / `password123`

#### Seed Food Data

1. Navigate to: `http://localhost/GoalCalories-P1/utils/seed_food_calories.php`
2. Page will execute and show success message
3. Creates 40 foods across 4 categories

### Option 2: Using SQL Script

1. **Open phpMyAdmin**
2. **Select `goalcaloriep1` database**
3. **Click "SQL" tab**
4. **Paste SQL from file**: `seed_food_calories.sql`
5. **Click "Go"**

### Option 3: Manual Database Import

1. Open command prompt/terminal
2. Navigate to project directory
3. Run:

```bash
mysql -u root goalcaloriep1 < seed_food_calories.sql
```

### Verify Seeded Data

```sql
-- Check test user
SELECT * FROM register WHERE email = 'test@example.com';

-- Check food count
SELECT COUNT(*) as total_foods FROM food_calories;
-- Should return: 40

-- Check food by category
SELECT category, COUNT(*) as count 
FROM food_calories 
GROUP BY category;
-- Should show 10 foods per category
```

---

## ✅ Verification

### Test Database Connection

1. Create test file: `C:\xampp\htdocs\GoalCalories-P1\test_connection.php`

```php
<?php
require_once 'config/database.php';

if ($conn) {
    echo "✅ Database connection successful!<br>";
    echo "Database: " . DB_NAME . "<br>";
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as count FROM register");
    $row = $result->fetch_assoc();
    echo "Users in database: " . $row['count'] . "<br>";
    
    $result = $conn->query("SELECT COUNT(*) as count FROM food_calories");
    $row = $result->fetch_assoc();
    echo "Foods in database: " . $row['count'] . "<br>";
} else {
    echo "❌ Database connection failed!";
}
?>
```

2. Navigate to: `http://localhost/GoalCalories-P1/test_connection.php`
3. Should see: ✅ Database connection successful!

### Test Application

1. **Home Page**
   - URL: `http://localhost/GoalCalories-P1/`
   - Should redirect to `pages/home.html`

2. **Registration**
   - Click "Register"
   - Fill form with test data
   - Submit
   - Check for success message

3. **Login**
   - Use test credentials: `test@example.com` / `password123`
   - Should redirect to dashboard

4. **Dashboard**
   - Should see clean interface
   - All sections visible
   - No JavaScript errors (check browser console: F12)

5. **Calculate Calories**
   - Enter profile data
   - Click "Calculate"
   - Should see BMR and daily goal

6. **Track Meals**
   - Enter calorie values for meals
   - Click "Store Data"
   - Should see success message

7. **Food Recommendations**
   - Click "Food Recommendations"
   - Should see analysis (after tracking ≥1 day)

8. **Reports**
   - Click "Display Report"
   - Should see data table
   - Charts should render

### Checklist

- [ ] XAMPP running (Apache + MySQL)
- [ ] Database `goalcaloriep1` created
- [ ] All 3 tables created
- [ ] Sample data seeded
- [ ] Home page loads
- [ ] Registration works
- [ ] Login works
- [ ] Dashboard displays
- [ ] Calculator functions
- [ ] Data storage works
- [ ] Recommendations load
- [ ] Charts render

---

## 🔧 Troubleshooting Setup

### Issue: Database Connection Failed

**Symptoms:**
- "Database connection failed" error
- Pages show connection errors

**Solutions:**
1. Verify MySQL is running in XAMPP Control Panel
2. Check database name is exactly `goalcaloriep1`
3. Verify credentials in `config/database.php`
4. Test connection with test_connection.php

### Issue: Tables Not Found

**Symptoms:**
- "Table 'goalcaloriep1.register' doesn't exist"

**Solutions:**
1. Re-run table creation SQL
2. Check you selected correct database
3. Verify table names match exactly

### Issue: PHP Errors

**Symptoms:**
- White screen
- Parse errors
- Function undefined errors

**Solutions:**
1. Check PHP version: `php -v` (must be 7.4+)
2. Enable error display (for development only):
   ```php
   // Add to top of index.php temporarily
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
3. Check Apache error logs: `C:\xampp\apache\logs\error.log`

### Issue: Port Already in Use

**Symptoms:**
- "Port 80 in use by another program"
- Apache won't start

**Solutions:**
1. Change Apache port:
   - Edit `C:\xampp\apache\conf\httpd.conf`
   - Find: `Listen 80`
   - Change to: `Listen 8080`
   - Access via: `http://localhost:8080/`
2. Or stop conflicting service (Skype, IIS, etc.)

### Issue: Permission Denied

**Symptoms:**
- Can't write files
- Session errors

**Solutions:**

**Windows:**
```cmd
# Right-click GoalCalories-P1 folder
# Properties → Security → Edit
# Give "Users" full control
```

**macOS/Linux:**
```bash
sudo chmod -R 755 /opt/lampp/htdocs/GoalCalories-P1
sudo chown -R nobody:nogroup /opt/lampp/htdocs/GoalCalories-P1
```

### Issue: Charts Not Showing

**Symptoms:**
- Empty chart area
- Console error: "Chart.js not loaded"

**Solutions:**
1. Check internet connection (Chart.js loads from CDN)
2. Verify `dashboard.html` has Chart.js script:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
   ```
3. Check browser console for errors (F12)

### Issue: Session Not Working

**Symptoms:**
- "User not logged in" after login
- Logged out immediately

**Solutions:**
1. Check session configuration in php.ini
2. Ensure session_start() is called
3. Check browser cookies are enabled
4. Clear browser cache and cookies

---

## 🎓 Advanced Setup

### Setting Up Virtual Host (Optional)

Create custom URL like `http://goalcalories.local`

1. **Edit hosts file**

   **Windows:** `C:\Windows\System32\drivers\etc\hosts`
   ```
   127.0.0.1  goalcalories.local
   ```

   **macOS/Linux:** `/etc/hosts`
   ```
   127.0.0.1  goalcalories.local
   ```

2. **Edit Apache config**

   `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/GoalCalories-P1"
       ServerName goalcalories.local
       <Directory "C:/xampp/htdocs/GoalCalories-P1">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. **Restart Apache**

4. **Access:** `http://goalcalories.local`

### SSL Setup (HTTPS)

For development HTTPS:

1. Generate self-signed certificate
2. Configure Apache SSL
3. Update config to use HTTPS URLs

(Detailed instructions in Apache documentation)

---

## 📊 Post-Installation

### Recommended Next Steps

1. **Create Admin User**
   - Register through interface
   - Manually set admin flag in database

2. **Backup Database**
   ```bash
   mysqldump -u root goalcaloriep1 > backup.sql
   ```

3. **Review Security**
   - Change default passwords
   - Configure firewall
   - Review permissions

4. **Customize**
   - Update branding
   - Modify colors in CSS
   - Add your college name/logo

---

## 🎯 Setup Complete!

You should now have:
- ✅ Working web server
- ✅ Database with tables
- ✅ Sample data loaded
- ✅ Application accessible
- ✅ All features functional

**Next:** Read the [USER_GUIDE.md](USER_GUIDE.md) to learn how to use the application!

---

<div align="center">

**Need Help?** Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

[Back to Main README](../README.md) | [User Guide](USER_GUIDE.md) | [API Reference](API_REFERENCE.md)

</div>
