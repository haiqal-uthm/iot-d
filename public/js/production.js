    // 🔁 Global Chart instances (to avoid duplicates)
    let fallChart = null;
    let harvestChart = null;
    let inventoryChart = null;
    let harvesterPad = null;

    // ⏳ Wait for page load
    document.addEventListener("DOMContentLoaded", function () {
        renderFallChart();
        renderHarvestChart();
        renderInventoryChart();

        // Initialize signature pad if canvas exists
        const canvas = document.getElementById('harvesterSignature');
        if (canvas) {
            harvesterPad = new SignaturePad(canvas);
        }
    });

    // 🔷 Durian Fall Chart
    function renderFallChart() {
        if (fallChart) fallChart.destroy();

        const ctx = document.getElementById("fallChart").getContext("2d");
        const rawData = chartData; // Laravel variable

        const dates = Object.keys(rawData);
        const counts = Object.values(rawData);

        fallChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
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
                        title: { display: true, text: 'Date' },
                        ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 }
                    },
                    y: {
                        title: { display: true, text: 'Number of Falls' },
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    zoom: {
                        pan: { enabled: true, mode: 'x', threshold: 10 },
                        zoom: {
                            wheel: { enabled: true },
                            pinch: { enabled: true },
                            mode: 'x'
                        }
                    }
                }
            }
        });
    }

    // 🍃 Harvest Durian Line Chart
    function renderHarvestChart() {
        if (harvestChart) harvestChart.destroy();

        const ctx = document.getElementById("harvestChart").getContext("2d");
        const rawData = harvestReports;

        let groupedData = {};
        rawData.forEach(report => {
            let month = new Date(report.harvest_date).toISOString().slice(0, 7); // YYYY-MM
            let type = report.durian_type;

            groupedData[type] = groupedData[type] || {};
            groupedData[type][month] = (groupedData[type][month] || 0) + report.total_harvested;
        });

        let allMonths = Object.values(groupedData).flatMap(obj => Object.keys(obj));
        let uniqueMonths = [...new Set(allMonths)].sort();

        let datasets = Object.keys(groupedData).map(type => ({
            label: type,
            data: uniqueMonths.map(month => groupedData[type][month] || 0),
            borderColor: getRandomColor(),
            backgroundColor: "transparent",
            borderWidth: 2
        }));

        harvestChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: uniqueMonths,
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

        function getRandomColor() {
            return `hsl(${Math.random() * 360}, 70%, 60%)`;
        }
    }

    // 🧊 Inventory Bar Chart
    function renderInventoryChart() {
        if (inventoryChart) inventoryChart.destroy();

        const ctx = document.getElementById('inventoryChart').getContext('2d');
        const locations = inventoryData.map(item => `Storage ${item.storage_location}`);
        const quantities = inventoryData.map(item => item.total_quantity);

        inventoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: locations,
                datasets: [{
                    label: 'Storage Quantity (kg)',
                    data: quantities,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Quantity (kg)' }
                    },
                    x: {
                        title: { display: true, text: 'Storage Location' }
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Durian Storage Inventory' }
                }
            }
        });
    }

    // 🟢 Modal Open Function
    function openModal(id, date, orchard, durianType, totalHarvest) {
        let modal = document.querySelector('[x-data]');
        modal.__x.$data.showModal = true;
        modal.__x.$data.harvestId = id;
        modal.__x.$data.harvestDate = date;
        modal.__x.$data.orchard = orchard;
        modal.__x.$data.durianType = durianType;
        modal.__x.$data.totalHarvest = totalHarvest;
    }

    // 🖨️ Print Harvest Report
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

    // 🧼 Clear Signature Pad
    function clearSignature() {
        if (harvesterPad) {
            harvesterPad.clear();
        }
    }

    // 💾 Save Harvest Details via AJAX
    function saveHarvestDetails() {
        const form = document.getElementById('harvestDetailsForm');
        const formData = new FormData(form);

        fetch('/harvest-details', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.success ? "✅ Success!" : "⚠️ Error!",
                text: data.success ? "Harvest details saved successfully!" : (data.message || "Error saving harvest details"),
                icon: data.success ? "success" : "error",
                confirmButtonText: "OK",
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white'
                }
            }).then(() => {
                if (data.success) {
                    window.location.reload();
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "⚠️ Error!",
                text: "Failed to save harvest details",
                icon: "error",
                confirmButtonText: "OK",
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white'
                }
            });
        });
    }

    // 📋 Show Table + Trigger Corresponding Chart
    function showTable(tableId) {
        document.querySelectorAll('.table-container').forEach(container => container.classList.add('hidden'));
        document.querySelectorAll('form[id$="Filter"]').forEach(form => form.classList.add('hidden'));

        const selectedTable = document.getElementById(tableId);
        if (selectedTable) {
            selectedTable.classList.remove('hidden');
            document.getElementById(tableId + 'Filter')?.classList.remove('hidden');

            if (tableId === 'inventoryReport') renderInventoryChart();
            else if (tableId === 'recordFall') renderFallChart();
            else if (tableId === 'harvestReport') renderHarvestChart();
        }
    }

