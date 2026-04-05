# 🔌 GoalCalories - API Reference

> Complete documentation for all backend API endpoints

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [API Endpoints](#api-endpoints)
4. [Error Handling](#error-handling)
5. [Rate Limiting](#rate-limiting)
6. [Examples](#examples)

---

## 📡 Overview

### Base URL

```
http://localhost/GoalCalories-P1/
```

### Response Format

All endpoints return JSON responses:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { }
}
```

### Common Headers

```http
Content-Type: application/json
Cookie: PHPSESSID=<session_id>
```

### Session Management

- Sessions created on login
- Session expires after 30 minutes of inactivity
- Session required for all API calls except auth endpoints

---

## 🔐 Authentication

### 1. Register New User

**Endpoint:** `POST /auth/register.php`

**Description:** Creates a new user account

**Request Body:**
```json
{
  "fullname": "John Doe",
  "phonenumber": "1234567890",
  "email": "john@example.com",
  "password": "SecurePass123",
  "confirmpassword": "SecurePass123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Registration successful",
  "redirect": "pages/login.html"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Passwords do not match"
}
```

**Validation Rules:**
- `fullname`: Required, 3-100 characters
- `phonenumber`: Required, 10-15 digits
- `email`: Required, valid email format, unique
- `password`: Required, minimum 8 characters
- `confirmpassword`: Must match password

**Example cURL:**
```bash
curl -X POST http://localhost/GoalCalories-P1/auth/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "John Doe",
    "phonenumber": "1234567890",
    "email": "john@example.com",
    "password": "SecurePass123",
    "confirmpassword": "SecurePass123"
  }'
```

---

### 2. User Login

**Endpoint:** `POST /auth/login.php`

**Description:** Authenticates user and creates session

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePass123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "fullname": "John Doe",
    "email": "john@example.com"
  },
  "redirect": "pages/dashboard.html"
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

**Side Effects:**
- Creates PHP session
- Sets `user_id`, `email`, `fullname` in session

**Example cURL:**
```bash
curl -X POST http://localhost/GoalCalories-P1/auth/login.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123"
  }'
```

---

## 📊 API Endpoints

### 3. Get Dashboard Data

**Endpoint:** `GET /api/dashboard.php`

**Description:** Retrieves user's calorie history

**Authentication:** Required (session)

**Query Parameters:**
- None (uses session user_id)

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "breakfastCalories": 400,
      "lunchCalories": 650,
      "snackCalories": 200,
      "dinnerCalories": 550,
      "totalCalories": 1800,
      "dailyCalories": 2000,
      "surplus": 0,
      "deficit": 200,
      "created_at": "2026-01-05 08:30:00"
    },
    {
      "id": 2,
      "breakfastCalories": 350,
      "lunchCalories": 600,
      "snackCalories": 150,
      "dinnerCalories": 500,
      "totalCalories": 1600,
      "dailyCalories": 2000,
      "surplus": 0,
      "deficit": 400,
      "created_at": "2026-01-04 09:15:00"
    }
  ]
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "User not logged in"
}
```

**Example JavaScript:**
```javascript
fetch('/api/dashboard.php', {
  credentials: 'include'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 4. Insert/Update Calories

**Endpoint:** `POST /api/insert_calories.php`

**Description:** Saves or updates daily calorie data (one entry per day per user)

**Authentication:** Required

**Request Body:**
```json
{
  "breakfastCalories": 400,
  "lunchCalories": 650,
  "snackCalories": 200,
  "dinnerCalories": 550,
  "dailyCalories": 2000
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Data stored successfully",
  "data": {
    "totalCalories": 1800,
    "surplus": 0,
    "deficit": 200,
    "status": "deficit"
  }
}
```

**Calculation Logic:**
```
totalCalories = breakfast + lunch + snack + dinner
surplus = (totalCalories > dailyCalories) ? (totalCalories - dailyCalories) : 0
deficit = (totalCalories < dailyCalories) ? (dailyCalories - totalCalories) : 0
```

**Upsert Logic:**
- Checks if entry exists for today's date
- **Update** if exists
- **Insert** if new

**Example cURL:**
```bash
curl -X POST http://localhost/GoalCalories-P1/api/insert_calories.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "breakfastCalories": 400,
    "lunchCalories": 650,
    "snackCalories": 200,
    "dinnerCalories": 550,
    "dailyCalories": 2000
  }'
```

---

### 5. Get 7-Day Analysis

**Endpoint:** `GET /api/get_7day_analysis.php`

**Description:** Calculates moving average and trends over last 7 days

**Authentication:** Required

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "days_tracked": 7,
    "date_range": "Dec 30 - Jan 5",
    "averages": {
      "breakfast": 380,
      "lunch": 620,
      "snacks": 180,
      "dinner": 530,
      "total": 1710,
      "daily_goal": 2000
    },
    "trends": {
      "status": "deficit",
      "avg_deficit": 290,
      "consistency_score": 85
    },
    "daily_breakdown": [
      {
        "date": "2026-01-05",
        "total": 1800,
        "goal": 2000,
        "status": "deficit"
      }
      // ... more days
    ]
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "No data found for the last 7 days"
}
```

**Calculations:**
```
Moving Average = SUM(last_7_days) / days_tracked
Consistency Score = (days_within_10%_of_goal / total_days) × 100
```

---

### 6. Get User Report

**Endpoint:** `GET /api/get_user_report.php`

**Description:** Generates detailed report with all historical data

**Authentication:** Required

**Query Parameters:**
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)
- Default: All data if no dates specified

**Success Response (200):**
```json
{
  "success": true,
  "report": {
    "user_info": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "summary": {
      "total_days": 30,
      "avg_daily_intake": 1850,
      "avg_daily_goal": 2000,
      "total_deficit": 4500,
      "total_surplus": 0
    },
    "data": [
      {
        "date": "2026-01-05",
        "breakfast": 400,
        "lunch": 650,
        "snacks": 200,
        "dinner": 550,
        "total": 1800,
        "goal": 2000,
        "deficit": 200,
        "surplus": 0
      }
      // ... more entries
    ],
    "charts_data": {
      "labels": ["Jan 1", "Jan 2", "Jan 3", ...],
      "actual": [1700, 1850, 1900, ...],
      "goal": [2000, 2000, 2000, ...]
    }
  }
}
```

**Example with Date Range:**
```javascript
fetch('/api/get_user_report.php?start_date=2026-01-01&end_date=2026-01-07', {
  credentials: 'include'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 7. Get Food Recommendations

**Endpoint:** `GET /api/get_food_recommendations.php`

**Description:** Generates personalized food recommendations using CBCAWS algorithm

**Authentication:** Required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Recommendations generated successfully",
  "analysis_period": {
    "days_tracked": 7,
    "date_range": "7 days"
  },
  "current_averages": {
    "breakfast": 150,
    "lunch": 650,
    "dinner": 550,
    "snacks": 250,
    "total": 1600,
    "daily_goal": 2000
  },
  "category_analysis": {
    "breakfast": {
      "current_calories": 150,
      "ideal_calories": 550,
      "current_percentage": 9.4,
      "ideal_percentage": 27.5,
      "calorie_gap": 400,
      "deviation_percentage": -65.8,
      "severity_score": 65,
      "status": "deficient"
    },
    "lunch": {
      "current_calories": 650,
      "ideal_calories": 650,
      "current_percentage": 40.6,
      "ideal_percentage": 32.5,
      "calorie_gap": 0,
      "deviation_percentage": 25.0,
      "severity_score": 0,
      "status": "balanced"
    },
    "dinner": {
      "current_calories": 550,
      "ideal_calories": 550,
      "current_percentage": 34.4,
      "ideal_percentage": 27.5,
      "calorie_gap": 0,
      "deviation_percentage": 25.1,
      "severity_score": 0,
      "status": "balanced"
    },
    "snacks": {
      "current_calories": 250,
      "ideal_calories": 250,
      "current_percentage": 15.6,
      "ideal_percentage": 12.5,
      "calorie_gap": 0,
      "deviation_percentage": 25.2,
      "severity_score": 0,
      "status": "balanced"
    }
  },
  "recommendations": [
    {
      "category": "Breakfast",
      "severity": "high",
      "message": "Your breakfast intake is significantly low. You're consuming only 9% of your daily calories at breakfast when the ideal is 28%. This may lead to low morning energy and increased hunger later in the day.",
      "current_avg": 150,
      "recommended_avg": 550,
      "calorie_gap": 400,
      "suggested_foods": [
        {
          "name": "Avocado toast with egg",
          "calories": 380,
          "protein": "18g",
          "benefits": "Healthy fats, high protein, keeps you full until lunch"
        },
        {
          "name": "Scrambled eggs with whole wheat toast",
          "calories": 350,
          "protein": "22g",
          "benefits": "High protein helps build muscle and provides sustained energy"
        },
        {
          "name": "Banana peanut butter smoothie",
          "calories": 320,
          "protein": "12g",
          "benefits": "Quick to make, portable, natural sugars for energy"
        }
      ]
    }
  ],
  "general_recommendations": {
    "overall_status": "deficit",
    "message": "You're averaging 400 calories below your daily goal. Consider increasing portion sizes or adding healthy snacks.",
    "tips": [
      "Increase breakfast calories to improve morning energy",
      "Consider meal prep to ensure consistent intake",
      "Stay hydrated - aim for 8 glasses of water daily"
    ]
  },
  "algorithm_info": {
    "name": "Category-Based Calorie Analysis with Weighted Scoring",
    "version": "1.0",
    "description": "Analyzes eating patterns across meal categories and provides prioritized, personalized food recommendations"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Insufficient data. Please track meals for at least 1 day."
}
```

**Algorithm Details:** See [ALGORITHM.md](ALGORITHM.md)

**Example Usage:**
```javascript
fetch('/api/get_food_recommendations.php', {
  credentials: 'include'
})
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      displayRecommendations(data.recommendations);
    }
  });
```

---

## ❌ Error Handling

### Standard Error Response

```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "details": { }
}
```

### Common Error Codes

| Code | Message | Cause |
|------|---------|-------|
| `AUTH_REQUIRED` | User not logged in | No active session |
| `INVALID_INPUT` | Invalid parameters | Missing/malformed request data |
| `NOT_FOUND` | Resource not found | No data for query |
| `DB_ERROR` | Database error | Database connection/query failed |
| `DUPLICATE_ENTRY` | Email already exists | Registration with existing email |
| `UNAUTHORIZED` | Invalid credentials | Wrong email/password |

### HTTP Status Codes

| Status | Meaning | When Used |
|--------|---------|-----------|
| 200 | OK | Successful request |
| 400 | Bad Request | Invalid input data |
| 401 | Unauthorized | Not logged in or invalid credentials |
| 404 | Not Found | No data available |
| 500 | Internal Server Error | Database/server error |

---

## ⚡ Rate Limiting

### Current Limits

- **No rate limiting implemented** (development)
- Recommended for production: 100 requests/minute per user

### Future Implementation

```php
// Suggested rate limit logic
if (requests_in_last_minute($user_id) > 100) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many requests. Please try again later.',
        'retry_after': 60
    ]);
    exit;
}
```

---

## 💡 Examples

### Complete User Flow Example

#### 1. Register User

```javascript
const registerUser = async () => {
  const response = await fetch('/auth/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      fullname: 'Jane Smith',
      phonenumber: '9876543210',
      email: 'jane@example.com',
      password: 'SecurePass456',
      confirmpassword: 'SecurePass456'
    })
  });
  
  const data = await response.json();
  console.log(data);
  // { success: true, message: "Registration successful" }
};
```

#### 2. Login

```javascript
const loginUser = async () => {
  const response = await fetch('/auth/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',  // Important for session cookies
    body: JSON.stringify({
      email: 'jane@example.com',
      password: 'SecurePass456'
    })
  });
  
  const data = await response.json();
  console.log(data);
  // { success: true, user: {...}, redirect: "pages/dashboard.html" }
};
```

#### 3. Track Daily Calories

```javascript
const trackCalories = async () => {
  const response = await fetch('/api/insert_calories.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({
      breakfastCalories: 450,
      lunchCalories: 680,
      snackCalories: 220,
      dinnerCalories: 580,
      dailyCalories: 2100
    })
  });
  
  const data = await response.json();
  console.log(data);
  // { success: true, data: { totalCalories: 1930, deficit: 170 } }
};
```

#### 4. Get Recommendations

```javascript
const getRecommendations = async () => {
  const response = await fetch('/api/get_food_recommendations.php', {
    credentials: 'include'
  });
  
  const data = await response.json();
  
  if (data.success) {
    data.recommendations.forEach(rec => {
      console.log(`${rec.category}: ${rec.severity}`);
      console.log(`Gap: ${rec.calorie_gap} calories`);
      rec.suggested_foods.forEach(food => {
        console.log(`- ${food.name} (${food.calories} cal)`);
      });
    });
  }
};
```

### Error Handling Example

```javascript
const apiCall = async (endpoint, options = {}) => {
  try {
    const response = await fetch(endpoint, {
      ...options,
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      }
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'API request failed');
    }
    
    if (!data.success) {
      throw new Error(data.message);
    }
    
    return data;
    
  } catch (error) {
    console.error('API Error:', error.message);
    
    // Handle specific errors
    if (error.message.includes('not logged in')) {
      window.location.href = '/pages/login.html';
    }
    
    throw error;
  }
};

// Usage
try {
  const data = await apiCall('/api/dashboard.php');
  console.log('Dashboard data:', data);
} catch (error) {
  alert('Failed to load dashboard');
}
```

### Batch Operations Example

```javascript
// Track multiple days programmatically
const batchTrackCalories = async (entries) => {
  const results = [];
  
  for (const entry of entries) {
    const response = await fetch('/api/insert_calories.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(entry)
    });
    
    const data = await response.json();
    results.push(data);
    
    // Wait 100ms between requests to avoid server overload
    await new Promise(resolve => setTimeout(resolve, 100));
  }
  
  return results;
};

// Usage
const weekData = [
  { breakfastCalories: 400, lunchCalories: 650, ... },
  { breakfastCalories: 380, lunchCalories: 700, ... },
  // ... more days
];

const results = await batchTrackCalories(weekData);
console.log(`Tracked ${results.length} days`);
```

---

## 🔧 Testing API Endpoints

### Using Browser Console

```javascript
// Quick test in browser DevTools (F12)
fetch('/api/dashboard.php', { credentials: 'include' })
  .then(r => r.json())
  .then(d => console.log(d));
```

### Using Postman

1. **Set up session:**
   - POST `/auth/login.php` first
   - Save cookies from response
   - Use cookies in subsequent requests

2. **Test endpoints:**
   - Import collection (see `postman_collection.json`)
   - Run tests sequentially

### Using cURL (Command Line)

```bash
# Login and save session
curl -X POST http://localhost/GoalCalories-P1/auth/login.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{"email":"test@example.com","password":"password123"}'

# Use session for API calls
curl http://localhost/GoalCalories-P1/api/dashboard.php \
  -b cookies.txt

# Track calories
curl -X POST http://localhost/GoalCalories-P1/api/insert_calories.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{"breakfastCalories":400,"lunchCalories":650,"snackCalories":200,"dinnerCalories":550,"dailyCalories":2000}'
```

---

## 📝 Notes

### Database Schema

API responses match database schema:

**register table:**
- id (INT) - Primary key
- fullname (VARCHAR)
- phonenumber (VARCHAR)
- email (VARCHAR) - Unique
- password (VARCHAR) - Hashed
- created_at (TIMESTAMP)

**daily_calories table:**
- id (INT) - Primary key
- user_id (INT) - Foreign key
- breakfastCalories (INT)
- lunchCalories (INT)
- snackCalories (INT)
- dinnerCalories (INT)
- totalCalories (INT) - Calculated
- dailyCalories (INT) - User's goal
- surplus (INT) - Calculated
- deficit (INT) - Calculated
- created_at (TIMESTAMP)

**food_calories table:**
- food_id (INT) - Primary key
- food_name (VARCHAR)
- calories_per_100g (INT)
- category (VARCHAR) - Breakfast/Lunch/Dinner/Snacks
- protein (VARCHAR)
- benefits (TEXT)

### Security Considerations

1. **Password Hashing:**
   ```php
   $hashed = password_hash($password, PASSWORD_BCRYPT);
   ```

2. **SQL Injection Prevention:**
   ```php
   $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
   $stmt->bind_param("s", $email);
   ```

3. **Session Security:**
   - Session timeout: 30 minutes
   - Regenerate session ID on login
   - Unset session on logout

4. **Input Validation:**
   - All inputs sanitized
   - Type checking enforced
   - Range validation for numeric fields

---

## 🚀 Best Practices

### Frontend Integration

```javascript
// Create API service module
const API = {
  baseURL: '/GoalCalories-P1',
  
  async call(endpoint, options = {}) {
    const response = await fetch(this.baseURL + endpoint, {
      ...options,
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      }
    });
    return response.json();
  },
  
  auth: {
    login: (email, password) => 
      API.call('/auth/login.php', {
        method: 'POST',
        body: JSON.stringify({ email, password })
      }),
    
    register: (userData) =>
      API.call('/auth/register.php', {
        method: 'POST',
        body: JSON.stringify(userData)
      })
  },
  
  calories: {
    getDashboard: () => API.call('/api/dashboard.php'),
    
    track: (data) => API.call('/api/insert_calories.php', {
      method: 'POST',
      body: JSON.stringify(data)
    })
  },
  
  recommendations: {
    get: () => API.call('/api/get_food_recommendations.php')
  }
};

// Usage
const data = await API.calories.getDashboard();
```

---

<div align="center">

**API Version:** 1.0  
**Last Updated:** January 5, 2026

[Back to Main README](../README.md) | [Algorithm Details](ALGORITHM.md) | [Troubleshooting](TROUBLESHOOTING.md)

</div>
