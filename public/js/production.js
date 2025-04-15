// 🔁 Global Chart instances (to avoid duplicates)
    let fallChart = null;
    let harvestChart = null;
    let inventoryChart = null;
    let harvesterPad = null;

    // ⏳ Wait for page load
    document.addEventListener("DOMContentLoaded", function () {
        // Only render the chart for the initially visible table
        // Instead of rendering all charts at once
        const visibleTable = document.querySelector('.table-container:not(.hidden)');
        if (visibleTable) {
            const tableId = visibleTable.id;
            if (tableId === 'recordFall') {
                renderFallChart();
            } else if (tableId === 'harvestReport') {
                renderHarvestChart();
            } else if (tableId === 'inventoryReport') {
                renderInventoryChart();
            }
        } else {
            // Default to record fall if no table is visible
            renderFallChart();
        }

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
        
        // Get unique durian types
        const durianTypes = [...new Set(harvestChartData.map(item => item.durian_type))];
        
        // Extract data for pie chart
        const data = durianTypes.map(type => 
            harvestChartData.find(item => item.durian_type === type)?.total_harvested || 0
        );
        
        // Generate colors for each segment
        const backgroundColors = durianTypes.map(() => getRandomColor());
        const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));
        
        harvestChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels: durianTypes,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: "right",
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    title: { 
                        display: true, 
                        text: 'Durian Harvest by Type',
                        font: {
                            size: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        function getRandomColor() {
            return `rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 0.7)`;
        }
    }

    // 🧊 Inventory Bar Chart
    // 🧊 Inventory Bar Chart
    function renderInventoryChart() {
        if (inventoryChart) inventoryChart.destroy();
    
        const ctx = document.getElementById('inventoryChart').getContext('2d');
        
        // Check if inventoryData is an array and has items
        if (!Array.isArray(inventoryData) || inventoryData.length === 0) {
            console.warn('No inventory data available or data is not in the expected format');
            return;
        }
        
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
    document.addEventListener("DOMContentLoaded", function () {
        const activeTab = sessionStorage.getItem('activeTab') || 'recordFall';
        showTable(activeTab);

        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            const url = new URL(link.href);
            url.searchParams.set('activeTab', activeTab);
            link.href = url.toString();
        });

        renderHarvestChart();
        renderInventoryChart();
    });

    function showTable(tableId) {
        // Hide all table containers
        document.querySelectorAll('.table-container').forEach(container => {
            container.classList.add('hidden');
        });

        // Show the selected table container
        document.getElementById(tableId).classList.remove('hidden');

        // Store the active tab in session storage to maintain state after page refresh
        sessionStorage.setItem('activeTab', tableId);
    }

