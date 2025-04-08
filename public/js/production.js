document.addEventListener("DOMContentLoaded", function () {
    renderFallChart();
    renderHarvestChart();
});

// 🟢 Durian Fall Line Graph (Last 30 Days)
function renderFallChart() {
    const ctx = document.getElementById("fallChart").getContext("2d");
    const rawData = chartData; // Laravel passed data

    // Convert object to arrays
    const dates = Object.keys(rawData);
    const counts = Object.values(rawData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => new Date(date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            })),
            datasets: [{
                label: 'Durian Falls (Last 30 Days)',
                data: counts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Falls'
                    },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x',
                        threshold: 10
                    },
                    zoom: {
                        wheel: {
                            enabled: true,
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x'
                    }
                }
            }
        }
    });
}

// 🟡 Harvest Durian Gantt Chart
function renderHarvestChart() {
    const ctx = document.getElementById("harvestChart").getContext("2d");

    // Convert PHP data to JavaScript
    let rawData = harvestReports;

    // Group data by Durian Type & Month
    let groupedData = {};
    rawData.forEach(report => {
        let month = new Date(report.harvest_date).toISOString().slice(0, 7); // YYYY-MM
        let type = report.durian_type;

        if (!groupedData[type]) {
            groupedData[type] = {};
        }
        if (!groupedData[type][month]) {
            groupedData[type][month] = 0;
        }

        groupedData[type][month] += report.total_harvested;
    });

    // Generate labels (Months)
    let allMonths = Object.values(groupedData).flatMap(obj => Object.keys(obj));
    let uniqueMonths = [...new Set(allMonths)].sort(); // Remove duplicates & sort

    // Prepare datasets for Chart.js
    let datasets = Object.keys(groupedData).map(type => {
        return {
            label: type,
            data: uniqueMonths.map(month => groupedData[type][month] || 0),
            borderColor: getRandomColor(),
            backgroundColor: "transparent",
            borderWidth: 2
        };
    });

    // Function for Random Colors
    function getRandomColor() {
        return `hsl(${Math.random() * 360}, 70%, 60%)`;
    }

    // Render Line Chart
    new Chart(ctx, {
        type: "line",
        data: {
            labels: uniqueMonths, // X-axis (Months)
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: "category",
                    title: { display: true, text: "Month" }
                },
                y: {
                    title: { display: true, text: "Total Harvested" },
                    beginAtZero: true
                }
            },
            plugins: { legend: { position: "top" } }
        }
    });
}

// 🔵 Show/Hide Tables
// Add this to your existing showTable function
function showTable(tableId) {
    // Existing code for showing/hiding tables
    document.querySelectorAll('.table-container').forEach(container => {
        container.classList.add('hidden');
    });
    document.getElementById(tableId).classList.remove('hidden');

    // Show/hide corresponding filter forms
    document.querySelectorAll('form[id$="Filter"]').forEach(form => {
        form.classList.add('hidden');
    });
    document.getElementById(tableId + 'Filter').classList.remove('hidden');
}

function openModal(id, date, orchard, durianType, totalHarvest) {
    let modal = document.querySelector('[x-data]');
    modal.__x.$data.showModal = true;
    modal.__x.$data.harvestId = id;
    modal.__x.$data.harvestDate = date;
    modal.__x.$data.orchard = orchard;
    modal.__x.$data.durianType = durianType;
    modal.__x.$data.totalHarvest = totalHarvest;
}

function printReport() {
    const content = document.getElementById('printable').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Harvest Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h2 { color: #111827; }
                    ul { margin-top: 10px; }
                </style>
            </head>
            <body>${content}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Initialize signature pad
let harvesterPad;

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('harvesterSignature');
    if (canvas) {
        harvesterPad = new SignaturePad(canvas);
    }
});

function clearSignature(canvasId) {
    if (harvesterPad) {
        harvesterPad.clear();
    }
}

function saveHarvestDetails() {
    const form = document.getElementById('harvestDetailsForm');
    const formData = new FormData(form);
    

    // Send to server
    fetch('/harvest-details', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Harvest details saved successfully!');
            window.location.reload();
        } else {
            alert('Error saving harvest details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving harvest details');
    });
}

// Add this at the beginning of your file
let isSubmitting = false;

// Add this function to handle form submission
