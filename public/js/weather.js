document.addEventListener('DOMContentLoaded', function () {
    fetch("/weather/current") // Ensure this route exists in web.php
        .then(response => response.json())
        .then(data => {
            const weatherDescriptions = {
                0: 'Clear sky', 1: 'Mainly clear', 2: 'Partly cloudy', 3: 'Overcast',
                45: 'Fog', 48: 'Fog', 51: 'Drizzle', 53: 'Drizzle',
                61: 'Rain showers', 63: 'Rain', 80: 'Rain showers',
                81: 'Rain showers', 82: 'Rain showers', 95: 'Thunderstorm',
                96: 'Thunderstorm with hail', 99: 'Thunderstorm with hail'
            };
            const iconMap = {
                0: '🌞', 1: '🌤️', 2: '🌥️', 3: '☁️', 45: '🌫️',
                48: '🌫️', 51: '🌧️', 53: '🌧️', 61: '🌦️', 63: '🌧️',
                80: '🌧️', 81: '🌧️', 82: '🌧️', 95: '⛈️', 96: '⛈️', 99: '⛈️'
            };

            if (data.error) {
                document.getElementById('weather-info').innerHTML = `<p>${data.error}</p>`;
                return;
            }

            // Display current weather
            const currentWeather = data.current_weather;
            if (currentWeather) {
                const icon = iconMap[currentWeather.weathercode] || '🌍';
                const temperature = `${currentWeather.temperature} °C`;
                const description = weatherDescriptions[currentWeather.weathercode] || 'Unknown weather';
                const windSpeed = `${currentWeather.windspeed} km/h`;
                const pressure = `${currentWeather.pressure} mbar`;
                const humidity = `${currentWeather.humidity}%`;

                document.getElementById('weather-info').innerHTML = `
                    <div id="weather-icon" class="text-6xl mb-4">${icon}</div>
                    <div class="text-5xl font-bold">${temperature}</div>
                    <div class="text-lg">${description}</div>
                `;

                document.getElementById('wind-speed').innerText = windSpeed;
                document.getElementById('pressure').innerText = pressure;
                document.getElementById('humidity').innerText = humidity;
            }

            // Display 3-day forecast
            const forecastContainer = document.getElementById('forecast');
            const forecast = data.forecast;
            if (forecast && forecast.time) {
                forecastContainer.innerHTML = ''; // Clear previous forecast
                forecast.time.forEach((date, index) => {
                    if (index < 3) { // Limit to 3 days
                        const forecastIcon = iconMap[forecast.weathercode[index]] || '🌍';
                        const forecastDescription = weatherDescriptions[forecast.weathercode[index]] || 'Unknown weather';
                        const forecastDate = new Date(date).toLocaleDateString();
                        const maxTemp = `${forecast.temperature_2m_max[index]} °C`;
                        const minTemp = `${forecast.temperature_2m_min[index]} °C`;

                        // Inject forecast card into container
                        forecastContainer.innerHTML += `
                            <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded-lg text-center shadow-lg shadow-gray-400 dark:shadow-gray-900/50">
                                <p class="font-bold">${forecastDate}</p>
                                <div class="text-4xl mb-2">${forecastIcon}</div>
                                <p class="text-lg">${forecastDescription}</p>
                                <p>🌡️ Max: ${maxTemp} / Min: ${minTemp}</p>
                            </div>
                        `;
                    }
                });
            }
        })
        .catch(error => {
            console.error("Error fetching weather data:", error);
            document.getElementById('weather-info').innerHTML = '<p>Error fetching weather data.</p>';
        });
});
