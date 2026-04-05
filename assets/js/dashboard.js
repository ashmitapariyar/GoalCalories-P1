// Version: 2.0 - Updated Jan 5, 2026 - Clean UI
// Global variables for tracking calories
let dailyCalories = 0;
let totalCalories = 0;
let surplus = 0;
let deficit = 0;

// Global variables for meal calorie totals
let breakfastCalories = 0;
let lunchCalories = 0;
let snackCalories = 0;
let dinnerCalories = 0;

// Function to calculate daily calorie requirement
function calculateCalories(event) {
    event.preventDefault();

    let age = parseInt(document.getElementById('age').value) || 0;
    let weight = parseFloat(document.getElementById('weight').value) || 0;
    let height = parseFloat(document.getElementById('height').value) || 0;
    let gender = document.getElementById('gender').value;

    if (age <= 0 || weight <= 0 || height <= 0 || !gender) {
        alert("Please enter valid age, weight, height, and gender.");
        return;
    }

    let BMR = 10 * weight + 6.25 * height - 5 * age;
    BMR += gender === 'male' ? 5 : -161;

    dailyCalories = BMR * 1.2; // Sedentary activity multiplier
    document.getElementById('calorie-result').textContent = `Your daily calorie requirement is ${Math.round(dailyCalories)} calories.`;
}

// Function to track food calories
function trackFoodCalories(event) {
    event.preventDefault();

    let foodItems = [
        { id: 'tea', calories: 30, meal: 'breakfast' },
        { id: 'egg', calories: 70, meal: 'breakfast' },
        { id: 'bread', calories: 80, meal: 'breakfast' },
        { id: 'gram', calories: 100, meal: 'breakfast' },
        { id: 'milk', calories: 150, meal: 'breakfast' },

        { id: 'rice', calories: 200, meal: 'lunch' },
        { id: 'daal', calories: 100, meal: 'lunch' },
        { id: 'vegetables', calories: 150, meal: 'lunch' },
        { id: 'pickles', calories: 50, meal: 'lunch' },
        { id: 'non-veg-lunch', calories: 300, meal: 'lunch' },

        { id: 'momo', calories: 350, meal: 'snacks' },
        { id: 'chips', calories: 150, meal: 'snacks' },
        { id: 'samosa', calories: 200, meal: 'snacks' },
        { id: 'noodles', calories: 250, meal: 'snacks' },
        { id: 'roti', calories: 120, meal: 'snacks' },

        { id: 'dinner-rice', calories: 200, meal: 'dinner' },
        { id: 'dinner-daal', calories: 100, meal: 'dinner' },
        { id: 'curry', calories: 250, meal: 'dinner' },
        { id: 'chicken', calories: 300, meal: 'dinner' },
    ];


    breakfastCalories = lunchCalories = snackCalories = dinnerCalories = 0;

    foodItems.forEach(item => {
        let quantity = parseInt(document.getElementById(item.id).value) || 0;
        let totalItemCalories = item.calories * quantity;

        switch (item.meal) {
            case 'breakfast': breakfastCalories += totalItemCalories; break;
            case 'lunch': lunchCalories += totalItemCalories; break;
            case 'snacks': snackCalories += totalItemCalories; break;
            case 'dinner': dinnerCalories += totalItemCalories; break;
        }
    });

    totalCalories = breakfastCalories + lunchCalories + snackCalories + dinnerCalories;
    document.getElementById('food-calorie-result').textContent = `You have consumed ${totalCalories} calories today.`;
}





// Function to save calorie data to the database
function saveCaloriesToDatabase() {
    // Validation: Check if calories have been calculated
    if (totalCalories === 0) {
        alert("Please track your food calories first before storing!");
        return;
    }

    if (dailyCalories === 0) {
        alert("Please calculate your daily calorie requirement first!");
        return;
    }

    // Calculate surplus and deficit
    if (totalCalories > dailyCalories) {
        surplus = totalCalories - dailyCalories;
        deficit = 0;
    } else if (totalCalories < dailyCalories) {
        deficit = dailyCalories - totalCalories;
        surplus = 0;
    } else {
        surplus = deficit = 0;
    }

    let data = {
        breakfastCalories: breakfastCalories,
        lunchCalories: lunchCalories,
        snackCalories: snackCalories,
        dinnerCalories: dinnerCalories,
        totalCalories: totalCalories,
        dailyCalories: dailyCalories,
        surplus: surplus,
        deficit: deficit
    };

    // Show loading state
    const storeBtn = document.getElementById("store-btn");
    const originalText = storeBtn.textContent;
    storeBtn.textContent = "Saving...";
    storeBtn.disabled = true;

    fetch('../api/insert_calories.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Different messages for update vs insert
            if (result.action === 'updated') {
                alert("✓ " + result.message + "\n\nNote: Your previous entry for today has been replaced with the new data.");
            } else {
                alert("✓ " + result.message);
            }
        } else {
            alert("✗ Error: " + result.message);
        }

    })
    .catch(error => {
        alert("✗ Error storing data: " + error.message);
    })
    .finally(() => {
        // Restore button state
        storeBtn.textContent = originalText;
        storeBtn.disabled = false;
    });
}

// Function to fetch and display calorie data
function fetchCaloriesData() {
    fetch("../api/dashboard.php")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector("#calorie-table tbody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='9'>No data available</td></tr>";
                document.getElementById('chart-container').style.display = 'none';
            } else {
                // Populate table
                data.forEach(row => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.breakfastCalories}</td>
                        <td>${row.lunchCalories}</td>
                        <td>${row.snackCalories}</td>
                        <td>${row.dinnerCalories}</td>
                        <td>${row.totalCalories}</td>
                        <td>${row.dailyCalories}</td>
                        <td>${row.surplus}</td>
                        <td>${row.deficit}</td>
                        <td>${row.created_at}</td>
                    `;
                    tableBody.appendChild(tr);
                });

                // Show and render charts
                document.getElementById('chart-container').style.display = 'block';
                renderCharts(data);
            }
        })
        .catch(error => {
            alert("Error fetching data.");
        });
}

// Global chart instances
let calorieChart = null;
let mealDistributionChart = null;

// Function to render eating pattern charts
function renderCharts(data) {
    // Destroy existing charts if they exist
    if (calorieChart) {
        calorieChart.destroy();
    }
    if (mealDistributionChart) {
        mealDistributionChart.destroy();
    }

    // Prepare data for charts (last 7 days or all data)
    const chartData = data.slice(0, 7).reverse(); // Show most recent 7 days
    
    // Chart 1: Daily Calorie Trends (Line Chart)
    const dates = chartData.map(row => {
        const date = new Date(row.created_at);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const totalCalories = chartData.map(row => row.totalCalories);
    const dailyGoals = chartData.map(row => row.dailyCalories);
    
    const ctx1 = document.getElementById('calorieChart').getContext('2d');
    calorieChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Actual Intake',
                    data: totalCalories,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Daily Goal',
                    data: dailyGoals,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' cal';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' cal';
                        },
                        font: { size: 11 }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 11 }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Chart 2: Meal Distribution (Doughnut Chart)
    // Calculate average calories per meal category
    const avgBreakfast = chartData.reduce((sum, row) => sum + parseInt(row.breakfastCalories), 0) / chartData.length;
    const avgLunch = chartData.reduce((sum, row) => sum + parseInt(row.lunchCalories), 0) / chartData.length;
    const avgSnacks = chartData.reduce((sum, row) => sum + parseInt(row.snackCalories), 0) / chartData.length;
    const avgDinner = chartData.reduce((sum, row) => sum + parseInt(row.dinnerCalories), 0) / chartData.length;
    
    const ctx2 = document.getElementById('mealDistributionChart').getContext('2d');
    mealDistributionChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['🌅 Breakfast', '🌞 Lunch', '🌙 Dinner', '🍎 Snacks'],
            datasets: [{
                data: [
                    Math.round(avgBreakfast),
                    Math.round(avgLunch),
                    Math.round(avgDinner),
                    Math.round(avgSnacks)
                ],
                backgroundColor: [
                    '#fbbf24', // Yellow for breakfast
                    '#f59e0b', // Orange for lunch
                    '#6366f1', // Blue for dinner
                    '#10b981'  // Green for snacks
                ],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return label + ': ' + value + ' cal (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
}

// Add event listeners for buttons
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("store-btn").addEventListener("click", saveCaloriesToDatabase);
    document.getElementById("display-btn").addEventListener("click", fetchCaloriesData);
    document.getElementById("analyze-btn").addEventListener("click", fetch7DayAnalysis);
    
    const recommendationBtn = document.getElementById("get-recommendations-btn");
    if (recommendationBtn) {
        recommendationBtn.addEventListener("click", getFoodRecommendations);
        console.log("Food recommendations button initialized");
    } else {
        console.error("Food recommendations button not found!");
    }
});

/**
 * 7-Day Moving Average Analysis Function
 * Fetches and displays the 7-day calorie analysis
 */
function fetch7DayAnalysis() {
    // Show loading state
    const analyzeBtn = document.getElementById("analyze-btn");
    const originalText = analyzeBtn.textContent;
    analyzeBtn.textContent = "Analyzing...";
    analyzeBtn.disabled = true;

    fetch("../api/get_7day_analysis.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display the results
                displayAnalysisResults(data.analysis);
            } else {
                alert(data.message || "Error fetching analysis data.");
            }
        })
        .catch(error => {
            alert("Error fetching analysis: " + error.message);
        })
        .finally(() => {
            // Restore button state
            analyzeBtn.textContent = originalText;
            analyzeBtn.disabled = false;
        });
}

/**
 * Display Analysis Results
 * Updates the UI with calculated values
 */
function displayAnalysisResults(analysis) {
    const resultContainer = document.getElementById("analysis-result");
    resultContainer.style.display = "block";

    document.getElementById("day-count").textContent = analysis.day_count + (analysis.day_count === 1 ? " day" : " days");
    document.getElementById("total-calories").textContent = analysis.total_calories.toFixed(2) + " cal";
    document.getElementById("avg-calories").textContent = analysis.average_calories.toFixed(2) + " cal";
    document.getElementById("req-calories").textContent = analysis.daily_requirement.toFixed(2) + " cal";

    const statusBadge = document.getElementById("status-badge");
    const intakeStatus = document.getElementById("intake-status");
    
    statusBadge.className = "status-badge-new";
    statusBadge.classList.add(analysis.status_class);
    intakeStatus.textContent = analysis.intake_status;

    document.getElementById("recommendation-text").textContent = analysis.recommendation;

    resultContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Get Food Recommendations
 * Fetches personalized food recommendations based on calorie status
 */
function getFoodRecommendations() {
    const recommendationBtn = document.getElementById("get-recommendations-btn");
    const originalText = recommendationBtn.textContent;
    recommendationBtn.textContent = "Loading...";
    recommendationBtn.disabled = true;

    console.log("Fetching food recommendations...");

    fetch("../api/get_food_recommendations.php")
        .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log("Raw response:", text);
            try {
                const data = JSON.parse(text);
                console.log("Parsed data:", data);
                if (data.success) {
                    displayFoodRecommendations(data);
                } else {
                    alert(data.message || "Error fetching recommendations.");
                }
            } catch (e) {
                console.error("JSON parse error:", e);
                alert("Error parsing response. Check console for details.");
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Error: " + error.message);
        })
        .finally(() => {
            recommendationBtn.textContent = originalText;
            recommendationBtn.disabled = false;
        });
}

/**
 * Display Food Recommendations
 * Shows recommended foods in the UI with category-based analysis
 */
function displayFoodRecommendations(data) {
    console.log("Displaying recommendations with data:", data);
    
    const statusDiv = document.getElementById("recommendation-status");
    const recommendationsDiv = document.getElementById("food-recommendations");
    
    if (!statusDiv || !recommendationsDiv) {
        console.error("Required elements not found!", {statusDiv, recommendationsDiv});
        alert("Error: UI elements not found. Please refresh the page.");
        return;
    }
    
    // Show status - Updated for new data structure
    statusDiv.style.display = "block";
    document.getElementById("rec-message").textContent = data.message;
    document.getElementById("rec-total-cal").textContent = data.current_averages.total + " cal";
    document.getElementById("rec-goal-cal").textContent = data.current_averages.daily_goal + " cal";
    
    // Calculate overall status
    const totalDiff = data.current_averages.total - data.current_averages.daily_goal;
    let statusText = "";
    if (totalDiff < -50) {
        statusText = `⚠️ Deficit: ${Math.abs(totalDiff).toFixed(0)} cal/day`;
    } else if (totalDiff > 50) {
        statusText = `⚡ Surplus: ${totalDiff.toFixed(0)} cal/day`;
    } else {
        statusText = "🎯 On Target!";
    }
    document.getElementById("rec-status").textContent = statusText;
    
    // Display category analysis summary
    let categoryHTML = '<div class="category-overview"><h3>📊 Your Meal Distribution</h3><div class="category-grid">';
    
    const categories = ['breakfast', 'lunch', 'dinner', 'snacks'];
    const categoryIcons = {
        'breakfast': '🌅',
        'lunch': '🌞',
        'dinner': '🌙',
        'snacks': '🍎'
    };
    
    categories.forEach(cat => {
        const analysis = data.category_analysis[cat];
        const statusClass = analysis.status === 'deficient' ? 'status-low' : 
                          (analysis.status === 'excess' ? 'status-high' : 'status-balanced');
        
        categoryHTML += `
            <div class="category-card ${statusClass}">
                <div class="category-icon">${categoryIcons[cat]}</div>
                <h4>${cat.charAt(0).toUpperCase() + cat.slice(1)}</h4>
                <div class="category-stats">
                    <p><strong>${analysis.current_calories}</strong> cal</p>
                    <p class="small">Goal: ${analysis.ideal_calories} cal</p>
                    <p class="percentage">${analysis.current_percentage}%</p>
                </div>
                <span class="status-badge ${statusClass}">${analysis.status}</span>
            </div>
        `;
    });
    
    categoryHTML += '</div></div>';
    
    // Display detailed recommendations
    let recHTML = '<div class="recommendations-container"><h3>💡 Personalized Recommendations</h3>';
    
    if (data.recommendations && data.recommendations.length > 0) {
        data.recommendations.forEach((rec, index) => {
            const severityClass = rec.severity === 'high' ? 'severity-high' : 
                                (rec.severity === 'medium' ? 'severity-medium' : 'severity-low');
            
            recHTML += `
                <div class="recommendation-section ${severityClass}">
                    <div class="rec-header">
                        <h4>${rec.category}</h4>
                        ${rec.severity !== 'none' ? `<span class="severity-badge ${severityClass}">${rec.severity} priority</span>` : ''}
                    </div>
                    <p class="rec-message">${rec.message}</p>
                    <div class="rec-stats">
                        <div class="stat">
                            <span class="label">Current Avg:</span>
                            <span class="value">${rec.current_avg} cal</span>
                        </div>
                        <div class="stat">
                            <span class="label">Recommended:</span>
                            <span class="value">${rec.recommended_avg} cal</span>
                        </div>
                        ${rec.calorie_gap > 0 ? `
                        <div class="stat gap">
                            <span class="label">Gap:</span>
                            <span class="value">+${rec.calorie_gap} cal needed</span>
                        </div>
                        ` : ''}
                    </div>
            `;
            
            // Display suggested foods
            if (rec.suggested_foods && rec.suggested_foods.length > 0) {
                recHTML += '<div class="food-suggestions"><h5>🍽️ Suggested Foods</h5><div class="food-grid">';
                rec.suggested_foods.forEach(food => {
                    recHTML += `
                        <div class="food-card">
                            <div class="food-name">${food.name}</div>
                            <div class="food-calories">${food.calories} cal/100g</div>
                            <div class="food-category">Category: ${food.category}</div>
                        </div>
                    `;
                });
                recHTML += '</div></div>';
            }
            
            recHTML += '</div>';
        });
    } else {
        recHTML += '<p class="no-recommendations">Great job! Your nutrition is well balanced. 🎉</p>';
    }
    
    recHTML += '</div>';
    
    // Display algorithm info
    const algoHTML = `
        <div class="algorithm-info">
            <p><strong>Analysis:</strong> ${data.algorithm_info.name}</p>
            <p><small>${data.algorithm_info.description}</small></p>
            <p><small>Based on ${data.analysis_period.days_tracked} days of data</small></p>
        </div>
    `;
    
    // Combine all HTML
    recommendationsDiv.innerHTML = categoryHTML + recHTML + algoHTML;
    
    console.log("Displayed recommendations for", data.recommendations.length, "categories");
    statusDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

