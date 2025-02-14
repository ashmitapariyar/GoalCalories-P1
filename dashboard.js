

// Global variables for tracking calories
let dailyCalories = 0; // Daily calorie requirement
let totalCalories = 0; // Total calories consumed
let surplus = 0; // Surplus calories
let deficit = 0; // Deficit calories

// Global variables for meal calorie totals
let breakfastCalories = 0;
let lunchCalories = 0;
let snackCalories = 0;
let dinnerCalories = 0;

// Function to show active section
function showSection(sectionId) {
    let sections = document.querySelectorAll('.section');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
}

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

    fetch('insert_calories.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.text())
    .then(message => {
        alert("Data stored successfully.");
        console.log(message); // Log the server response
    })
    .catch(error => {
        alert("Error storing data.");
        console.error("Error:", error);
    });
}

// Function to fetch and display calorie data
function fetchCaloriesData() {
    fetch("dashboard.php")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector("#calorie-table tbody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='9'>No data available</td></tr>";
            } else {
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
            }
        })
        .catch(error => {
            alert("Error fetching data.");
            console.error("Error:", error);
        });
}

// Add event listeners for buttons
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("store-btn").addEventListener("click", saveCaloriesToDatabase);
    document.getElementById("display-btn").addEventListener("click", fetchCaloriesData);
});
