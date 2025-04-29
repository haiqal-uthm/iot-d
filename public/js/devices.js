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
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}

function closeModal(modalId) {
    document.getElementById(modalId)?.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
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
    
    document.getElementById('editDeviceForm').action = `/devices/${device.id}`;

    openModal('editDeviceModal');
}

/**
 * Open Delete Confirmation Modal
 */
function openDeleteModal(deviceId, deviceName) {
    // Set the device name in the confirmation message
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete the device "${deviceName}"? This action cannot be undone.`;
    
    // Set up the confirm button to submit the form
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        document.getElementById(`delete-form-${deviceId}`).submit();
        closeModal('deleteConfirmModal');
    };
    
    // Open the modal
    openModal('deleteConfirmModal');
}

/**
 * Toggle LED Status
 */
function toggleLed(deviceId, isChecked) {
    // Show loading state
    const deviceCard = document.querySelector(`[data-device-id="${deviceId}"]`);
    if (deviceCard) {
        deviceCard.classList.add('opacity-75');
    }
    
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
            
            // Remove loading state
            deviceElement.classList.remove('opacity-75');
        }
        
        // Show success message
        showResponseModal('success', 'LED Status Updated', `The LED status has been set to ${data.status}`);
    })
    .catch(error => {
        console.error('Error toggling LED:', error);
        // Remove loading state and revert toggle on error
        if (deviceCard) {
            deviceCard.classList.remove('opacity-75');
            const checkbox = deviceCard.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.checked = !isChecked;
        }
        
        // Show error message
        showResponseModal('error', 'Error', 'Failed to update LED status. Please try again.');
    });
}

/**
 * Confirm Delete (Legacy method - replaced by modal)
 */
function confirmDelete(deviceName) {
    if (confirm(`Are you sure you want to delete the device "${deviceName}"?`)) {
        // The form will submit if user confirms
        setTimeout(() => {
            showResponseModal('success', 'Device Deleted', `The device "${deviceName}" has been successfully deleted.`);
        }, 1000);
        return true;
    }
    return false;
}

/**
 * Save Vibration Count (Collect)
 */
function saveVibrationCount(deviceId, count) {
    fetch('/update-total', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            device_id: deviceId,
            count: count
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResponseModal('success', 'Data Collected', 'The vibration count has been successfully collected and reset.');
        } else {
            showResponseModal('error', 'Error', 'Failed to collect vibration count. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error saving vibration count:', error);
        showResponseModal('error', 'Error', 'Failed to collect vibration count. Please try again.');
    });
}

/**
 * Show Response Modal
 */
function showResponseModal(type, title, message) {
    const modal = document.getElementById('responseModal');
    const iconElement = document.getElementById('responseIcon');
    const titleElement = document.getElementById('responseTitle');
    const messageElement = document.getElementById('responseMessage');
    
    // Set icon based on type
    let iconHtml = '';
    if (type === 'success') {
        iconHtml = '<div class="bg-green-100 rounded-full p-3 mx-auto inline-block"><i class="fas fa-check-circle text-3xl text-green-500"></i></div>';
    } else if (type === 'error') {
        iconHtml = '<div class="bg-red-100 rounded-full p-3 mx-auto inline-block"><i class="fas fa-times-circle text-3xl text-red-500"></i></div>';
    } else if (type === 'info') {
        iconHtml = '<div class="bg-blue-100 rounded-full p-3 mx-auto inline-block"><i class="fas fa-info-circle text-3xl text-blue-500"></i></div>';
    } else if (type === 'warning') {
        iconHtml = '<div class="bg-yellow-100 rounded-full p-3 mx-auto inline-block"><i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i></div>';
    }
    
    iconElement.innerHTML = iconHtml;
    titleElement.textContent = title;
    messageElement.textContent = message;
    
    openModal('responseModal');
}

/**
 * Initialize Event Listeners
 */
function initializeEventListeners() {
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modals = ['addDeviceModal', 'editDeviceModal', 'responseModal', 'deleteConfirmModal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            const modalContent = modal?.querySelector('.modal-content');
            
            if (modal && !modal.classList.contains('hidden') && 
                event.target === modal && modalContent && !modalContent.contains(event.target)) {
                closeModal(modalId);
            }
        });
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = ['addDeviceModal', 'editDeviceModal', 'responseModal', 'deleteConfirmModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });
    
    // Handle form submissions
    // Handle form submissions
    const addDeviceForm = document.querySelector('#addDeviceModal form');
    if (addDeviceForm) {
        addDeviceForm.addEventListener('submit', function(event) {
            // Let the form submit normally, but show a response after a delay
            setTimeout(() => {
                showResponseModal('success', 'Device Added', 'The new device has been successfully added.');
            }, 1000);
        });
    }
    
    // For delete, there's a confirmDelete function
    function confirmDelete(deviceName) {
        if (confirm(`Are you sure you want to delete the device "${deviceName}"?`)) {
            // The form will submit if user confirms
            setTimeout(() => {
                showResponseModal('success', 'Device Deleted', `The device "${deviceName}" has been successfully deleted.`);
            }, 1000);
            return true;
        }
        return false;
    }
    const editDeviceForm = document.querySelector('#editDeviceForm');
    if (editDeviceForm) {
        editDeviceForm.addEventListener('submit', function(event) {
            // Let the form submit normally, but show a response after a delay
            setTimeout(() => {
                showResponseModal('success', 'Device Updated', 'The device has been successfully updated.');
            }, 1000);
        });
    }
    
    // Check for flash messages from the server
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flashMessage !== 'undefined' && flashMessage) {
            showResponseModal(flashMessage.type, flashMessage.title, flashMessage.message);
        }
    });
}

/**
 * Load Firebase Scripts and Initialize
 */
loadScripts(() => {
    document.addEventListener('DOMContentLoaded', function () {
        initializeFirebase();
        initializeEventListeners();
        
        // Check for flash messages from the server
        if (typeof flashMessage !== 'undefined' && flashMessage) {
            showResponseModal(flashMessage.type, flashMessage.title, flashMessage.message);
        }
    });
});
