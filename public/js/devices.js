/**
 * Dynamically Load External Firebase Libraries
 */
function loadScripts(callback) {
    const scripts = [
        "https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js",
        "https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"
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

/**
 * Firebase Configuration & Initialization
 */
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

function initializeFirebase() {
    firebase.initializeApp(firebaseConfig);
}

/**
 * Modal Controls
 */
function openModal(modalId) {
    document.getElementById(modalId)?.classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId)?.classList.add('hidden');
}

/**
 * Open Edit Modal and Populate Fields
 */
function openEditModal(deviceId) {
    const device = durianData.find(device => device.id == deviceId);
    if (!device) return;

    document.getElementById('editDeviceId').value = device.id;
    document.getElementById('editDeviceName').value = device.name;
    
    // Set the status value in the dropdown
    if (document.getElementById('editDeviceStatus')) {
        document.getElementById('editDeviceStatus').value = device.status || 'active';
    }
    
    // Remove the orchard_id reference as it's no longer needed
    document.getElementById('editDeviceForm').action = `/devices/${device.id}`;

    openModal('editDeviceModal');
}

/**
 * Toggle LED Status
 */
function toggleLed(deviceId, isChecked) {
    // Implement LED toggle functionality with Firebase
    const status = isChecked ? 'ON' : 'OFF';
    
    fetch('/devices/toggle-led', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            device_id: deviceId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        // Update the LED status display
        const deviceElement = document.querySelector(`[data-device-id="${deviceId}"]`);
        if (deviceElement) {
            const statusElement = document.getElementById(`ledStatus-${deviceElement.id.split('-')[1]}`);
            if (statusElement) {
                statusElement.textContent = data.status;
            }
        }
    })
    .catch(error => console.error('Error toggling LED:', error));
}

/**
 * Load Firebase Scripts and Initialize
 */
loadScripts(() => {
    document.addEventListener('DOMContentLoaded', function () {
        initializeFirebase();
    });
});
