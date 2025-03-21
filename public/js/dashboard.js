// Load external libraries dynamically
function loadScripts(callback) {
    const scripts = [
        "https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js",
        "https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js",
        "https://cdn.jsdelivr.net/npm/chart.js"
    ];

    let loadedScripts = 0;

    scripts.forEach(src => {
        const script = document.createElement("script");
        script.src = src;
        script.onload = () => {
            loadedScripts++;
            if (loadedScripts === scripts.length) callback();
        };
        document.head.appendChild(script);
    });
}

// Run after scripts are loaded
loadScripts(() => {
    window.onload = function () {
        initializeFirebase();
        initializeDashboard();
        fetchWeatherData();
        setInterval(fetchCounts, 5000);
    };
});

/* --------------------------
   Firebase Configuration
---------------------------- */
const firebaseConfig = {
    apiKey: "AIzaSyD6DqAIqzO1Cz0H9dg_vJQU_zwswDcrZhM",
    authDomain: "iotd-85d25.firebaseapp.com",
    databaseURL: "https://iotd-85d25-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "iotd-85d25",
    storageBucket: "iotd-85d25.firebasestorage.app",
    messagingSenderId: "301410559515",
    appId: "1:301410559515:web:b9512f98d846e4d9defabe",
    measurementId: "G-YL7KJN3PXM"
};

// Initialize Firebase
function initializeFirebase() {
    firebase.initializeApp(firebaseConfig);
}

/* --------------------------
   Dashboard Functions
---------------------------- */
function initializeDashboard() {
    renderDurianChart();
    renderPieChart(durianData);
    setupVibrationListeners();
}

// Vibration Count Listener
function setupVibrationListeners() {
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    const sensor1Ref = database.ref("sensors/sensor1/vibrationCount");
    const sensor2Ref = database.ref("sensors/sensor2/vibrationCount");

    // Function to update the combined vibration count
    const sensorsRef = database.ref("sensors");
    sensorsRef.on("value", (snapshot) => {
        const sensorsData = snapshot.val();
        const totalCount = Object.values(sensorsData).reduce((sum, sensor) => sum + (sensor
            .vibrationCount || 0), 0);
        document.getElementById('vibration-count').innerText =
            `${totalCount}`;
    });


    // Variables to hold current counts from each sensor
    let sensor1Count = 0;
    let sensor2Count = 0;

    // Listener for Sensor 1
    sensor1Ref.on("value", (snapshot) => {
        sensor1Count = snapshot.val() || 0; // Default to 0 if no data
        updateCombinedCount(sensor1Count, sensor2Count);
    });

    // Listener for Sensor 2
    sensor2Ref.on("value", (snapshot) => {
        sensor2Count = snapshot.val() || 0; // Default to 0 if no data
        updateCombinedCount(sensor1Count, sensor2Count);
    });
}

// Fetch Weather Data
function fetchWeatherData() {
    console.log("Fetching weather from:", weatherRoute); // Debugging output

    fetch(weatherRoute)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Weather data received:", data); // Debugging output
            updateWeatherDisplay(data);
        })
        .catch(error => {
            console.error("Error fetching weather data:", error);
            document.getElementById('weather-info').innerHTML = '<p>Error fetching weather data.</p>';
        });
}

// Update Weather UI
function updateWeatherDisplay(data) {
    const weatherDescriptions = {
        0: 'Clear sky', 1: 'Mainly clear', 2: 'Partly cloudy', 3: 'Overcast',
        45: 'Fog', 48: 'Fog', 51: 'Drizzle', 53: 'Drizzle', 61: 'Rain showers',
        63: 'Rain', 80: 'Rain showers', 81: 'Rain showers', 82: 'Rain showers',
        95: 'Thunderstorm', 96: 'Thunderstorm with hail', 99: 'Thunderstorm with hail'
    };
    
    const iconMap = {
        0: '🌞', 1: '🌤️', 2: '🌥️', 3: '☁️', 45: '🌫️', 48: '🌫️', 51: '🌧️',
        53: '🌧️', 61: '🌦️', 63: '🌧️', 80: '🌧️', 81: '🌧️', 82: '🌧️',
        95: '⛈️', 96: '⛈️', 99: '⛈️'
    };

    if (data.error) {
        document.getElementById('weather-info').innerHTML = `<p>${data.error}</p>`;
        return;
    }

    const currentWeather = data.current_weather;
    if (currentWeather) {
        const icon = iconMap[currentWeather.weathercode] || '🌍';
        const temperature = `${currentWeather.temperature} °C`;
        const description = weatherDescriptions[currentWeather.weathercode] || 'Unknown weather';
        const windSpeed = `${currentWeather.windspeed} km/h`;

        document.getElementById('weather-info').innerHTML = `
            <div id="weather-icon w-50"><h1>${icon}</h1></div>
            <div class="text-2xl font-bold">${description}</div>
            <div class="text-2xl font-bold">${temperature}</div>
            <div class="text-2xl font-bold">${windSpeed}</div>
        `;
    }
}

// Fetch detection counts
function fetchCounts() {
    fetch('http://127.0.0.1:5000/counts') // Replace with actual server URL
        .then(response => response.json())
        .then(updateDetectionCounts)
        .catch(error => console.error("Error fetching detection counts:", error));
}

// Update Detection Counts UI
function updateDetectionCounts(data) {
    let countDisplay = document.getElementById('detectionCounts');
    countDisplay.innerHTML = ""; // Clear previous data

    if (Object.keys(data).length === 0) {
        countDisplay.innerHTML = "0";
    } else {
        for (const [label, count] of Object.entries(data)) {
            countDisplay.innerHTML += `<p>${label}: ${count}</p>`;
        }
    }
}

// Render Durian Chart
function renderDurianChart() {
    const chartCanvas = document.getElementById('durianChart');
    if (!chartCanvas) {
        console.error("Durian Chart Canvas not found!");
        return;
    }

    const ctx = chartCanvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Day 1", "Day 2", "Day 3", "Day 4", "Day 5", "Day 6", "Day 7"],
            datasets: [{
                label: 'Durian Falls in Last 7 Days',
                data: [5, 8, 3, 10, 6, 7, 9],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Count' }, beginAtZero: true }
            }
        }
    });
}

// Placeholder function for Pie Chart (needs implementation)
function renderPieChart(durianData) {
    const ctx = document.getElementById('pieChart').getContext('2d');

    if (!ctx) {
        console.error("Pie Chart Canvas not found!");
        return;
    }

    // Extract labels (names) and data (totals) from dataset
    const labels = durianData.map(item => item.name);
    const values = durianData.map(item => item.total);

    // Debugging output
    console.log("Chart Labels:", labels);
    console.log("Chart Values:", values);

    // Create the pie chart
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Durian Production',
                data: values,
                backgroundColor: ['#4CAF50', '#FFC107', '#FF5722', '#2196F3', '#9C27B0', '#E91E63'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return `${tooltipItem.label}: ${tooltipItem.raw}`;
                        }
                    }
                }
            }
        }
    });
}
