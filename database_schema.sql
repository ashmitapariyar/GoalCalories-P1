-- GoalCalories Database Schema
-- Database: goalcaloriep1
-- Version: 1.0
-- Last Updated: January 5, 2026

-- ============================================
-- CREATE DATABASE
-- ============================================

CREATE DATABASE IF NOT EXISTS goalcaloriep1 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE goalcaloriep1;

-- ============================================
-- TABLE 1: USER REGISTRATION
-- ============================================

CREATE TABLE IF NOT EXISTS register (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    phonenumber VARCHAR(15) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirmpassword VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User registration and authentication';

-- ============================================
-- TABLE 2: DAILY CALORIE TRACKING
-- ============================================

CREATE TABLE IF NOT EXISTS daily_calories (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Daily calorie intake tracking per user';

-- ============================================
-- TABLE 3: FOOD DATABASE
-- ============================================

CREATE TABLE IF NOT EXISTS food_calories (
    food_id INT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    calories_per_100g INT NOT NULL,
    category VARCHAR(20) CHECK (category IN ('Breakfast', 'Lunch', 'Snacks', 'Dinner')),
    protein VARCHAR(10),
    benefits TEXT,
    INDEX idx_category (category),
    INDEX idx_calories (calories_per_100g)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Food database for recommendations';

-- ============================================
-- VERIFY TABLES CREATED
-- ============================================

SHOW TABLES;

-- Expected output:
-- +---------------------------+
-- | Tables_in_goalcaloriep1   |
-- +---------------------------+
-- | daily_calories            |
-- | food_calories             |
-- | register                  |
-- +---------------------------+

-- ============================================
-- TABLE DESCRIPTIONS
-- ============================================

DESCRIBE register;
DESCRIBE daily_calories;
DESCRIBE food_calories;
