/* ==================================
   Load Firebase Libraries Dynamically
===================================== */
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

/* ==================================
   Firebase Configuration & Initialization
===================================== */
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
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    return firebase.database();
}

// Initialize Firebase after scripts load
loadScripts(() => {
    console.log("Firebase scripts loaded.");
    const database = initializeFirebase();
    
    if (typeof orchards !== "undefined" && Array.isArray(orchards)) {
        orchards.forEach(orchard => {
            fetchVibrationCount(database, orchard.id);
        });
        
        // Update total durian falls
        updateTotalDurianFalls();
    }
});

/* ===========================
   Firebase Vibration Data
============================== */
function fetchVibrationCount(database, sensorId) {
    const sensorRef = database.ref(`sensors/sensor${sensorId}`).child("vibrationCount");
    sensorRef.on('value', function(snapshot) {
        const vibrationCount = snapshot.val() || 0;
        const element = document.getElementById(`vibration-count-sensor-${sensorId}`);
        if (element) {
            element.innerText = vibrationCount;
            
            // Update total durian falls whenever any individual count changes
            updateTotalDurianFalls();
        }
    });
}

function updateTotalDurianFalls() {
    const totalElement = document.getElementById('total-durian-falls');
    if (totalElement) {
        let totalFalls = 0;
        const fallElements = document.querySelectorAll('[id^="vibration-count-sensor-"]');
        
        // Clear loading state
        totalElement.innerHTML = ''; 
        
        fallElements.forEach(el => {
            const count = parseInt(el.innerText, 10) || 0;
            totalFalls += count;
        });
        
        totalElement.innerText = totalFalls;
    }
}

function saveVibrationCount(orchardId, vibrationCount) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Get the full orchard data including durian relationship
    const orchard = orchards.find(o => o.id === parseInt(orchardId));
    
    if (!orchard?.durian?.id) {
        showResponseModal('error', 'Error!', 'No durian type assigned to this orchard.');
        return;
    }

    fetch("/durian/save-vibration", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            orchard_id: parseInt(orchardId),
            vibration_count: parseInt(vibrationCount, 10),
            durian_id: orchard.durian.id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResponseModal('success', 'Success!', 'Vibration count saved successfully!');
            resetVibrationCount(orchardId);
        } else {
            showResponseModal('error', 'Error!', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResponseModal('error', 'Error!', 'An error occurred while saving the vibration count.');
    });
}

function resetVibrationCount(orchardId) {
    const database = initializeFirebase();
    const sensorRef = database.ref(`sensors/sensor${orchardId}/vibrationCount`);
    sensorRef.set(0);
}

function showNotification(message, type) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.right = '20px';
        notification.style.padding = '10px 20px';
        notification.style.borderRadius = '5px';
        notification.style.color = 'white';
        notification.style.fontWeight = 'bold';
        notification.style.zIndex = '9999';
        notification.style.transition = 'opacity 0.5s ease-in-out';
        document.body.appendChild(notification);
    }
    
    // Set notification style based on type
    if (type === 'success') {
        notification.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#ef4444';
    } else {
        notification.style.backgroundColor = '#3b82f6';
    }
    
    // Set message and show notification
    notification.textContent = message;
    notification.style.opacity = '1';
    
    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
    }, 3000);
}

/* ===========================
   Modal Handling Functions
============================== */
function openViewModal(orchardId) {
    // Redirect to the orchard details page
    const role = document.body.dataset.userRole || 'farmer'; // Default to farmer if not set
    const baseUrl = role === 'farmer' ? '/farmer' : '';
    window.location.href = `${baseUrl}/orchards/${orchardId}`;
}

/* ===========================
   Modal Functions
============================== */
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}

function closeViewModal() {
    // Close all modals
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        modal.classList.add('hidden');
    });
    document.body.style.overflow = ''; // Restore scrolling
}

function openViewModal(orchardId) {
    // Find the orchard data
    const orchard = orchards.find(o => o.id === orchardId);
    
    if (orchard) {
        // Populate the modal with orchard data
        document.getElementById('nameOrchard').textContent = orchard.orchardName || 'N/A';
        document.getElementById('numTrees').textContent = orchard.numTree || 'N/A';
        document.getElementById('deviceName').textContent = orchard.device ? orchard.device.name : 'No Device Assigned';
        document.getElementById('durianName').textContent = orchard.durian ? orchard.durian.name : 'No Durian Assigned';
        
        // Set a placeholder image or actual orchard image if available
        const imgElement = document.getElementById('orchardImage');
        if (orchard.image_url) {
            imgElement.src = orchard.image_url;
            imgElement.alt = `${orchard.orchardName} Image`;
        } else {
            imgElement.src = '/images/orchard-placeholder.jpg';
            imgElement.alt = 'Orchard Placeholder Image';
        }
        
        // Show the modal
        openModal('viewModal');
    } else {
        console.error('Orchard not found:', orchardId);
        showResponseModal('error', 'Error', 'Orchard information could not be loaded.');
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('addOrchardModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

/**
 * Shows a response modal with customizable content
 * @param {string} message - The message to display
 * @param {string} type - The type of message (success, error, warning, info)
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function to execute after modal is closed
 */
function showResponseModal(message, type = 'info', title = null, callback = null) {
    // Create modal if it doesn't exist
    if (!document.getElementById('responseModal')) {
        createResponseModal();
    }
    
    // Set default title based on type if not provided
    if (!title) {
        switch (type) {
            case 'success':
                title = 'Success!';
                break;
            case 'error':
                title = 'Error!';
                break;
            case 'warning':
                title = 'Warning!';
                break;
            case 'info':
            default:
                title = 'Information';
                break;
        }
    }
    
    // Get modal elements
    const modal = document.getElementById('responseModal');
    const titleElement = document.getElementById('responseTitle');
    const messageElement = document.getElementById('responseMessage');
    const iconElement = document.getElementById('responseIcon');
    
    // Set content
    titleElement.textContent = title;
    messageElement.textContent = message;
    
    // Set icon based on type
    let iconHtml = '';
    if (type === 'success') {
        iconHtml = `
            <svg class="w-12 h-12 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        `;
    } else if (type === 'error') {
        iconHtml = `
            <svg class="w-12 h-12 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        `;
    } else if (type === 'warning') {
        iconHtml = `
            <svg class="w-12 h-12 text-yellow-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        `;
    } else {
        iconHtml = `
            <svg class="w-12 h-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        `;
    }
    
    iconElement.innerHTML = iconHtml;
    
    // Store callback if provided
    if (callback) {
        modal.dataset.callback = callback.toString();
    } else {
        delete modal.dataset.callback;
    }
    
    // Show the modal
    openModal('responseModal');
}

/**
 * Create the response modal HTML structure
 */
function createResponseModal() {
    const modalHTML = `
    <div id="responseModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <div class="text-center">
                <h3 id="responseTitle" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"></h3>
                <p id="responseMessage" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                <button onclick="closeModal('responseModal')" class="btn-primary w-full">
                    Close
                </button>
            </div>
        </div>
    </div>`;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add event listener for modal hidden event
    const modal = document.getElementById('responseModal');
    modal.addEventListener('hidden', function() {
        if (this.dataset.callback) {
            // Execute the callback function
            const callback = new Function('return ' + this.dataset.callback)();
            callback();
        }
    });
}

/**
 * Show a confirmation modal with Yes/No options
 * @param {string} message - The confirmation message
 * @param {function} confirmCallback - Function to call when user confirms
 * @param {function} cancelCallback - Optional function to call when user cancels
 * @param {string} title - Optional custom title
 */
function confirmDelete(event, orchardName) {
    event.preventDefault();
    
    // Create modal if it doesn't exist
    if (!document.getElementById('confirmModal')) {
        createConfirmModal();
    }
    
    const form = event.target.closest('form');
    const message = `Are you sure you want to delete the orchard "${orchardName}"? This action cannot be undone.`;
    const title = 'Confirm Deletion';
    
    // Get modal elements
    const modal = document.getElementById('confirmModal');
    const modalTitle = document.getElementById('confirmModalTitle');
    const modalBody = document.getElementById('confirmModalBody');
    const confirmBtn = document.getElementById('confirmModalYesBtn');
    
    // Set content
    modalTitle.textContent = title;
    modalBody.textContent = message;
    
    // Set up confirm button
    confirmBtn.onclick = function() {
        closeModal('confirmModal');
        form.submit();
    };
    
    // Show the modal
    openModal('confirmModal');
    
    return false;
}

/**
 * Create the confirmation modal HTML structure
 */
function createConfirmModal() {
    const modalHTML = `
    <div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6  max-w-md modal-content">
            <div class="text-center">
                <div class="bg-red-100 rounded-full p-3 mx-auto inline-block mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 id="confirmModalTitle" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"></h3>
                <p id="confirmModalBody" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                <div class="flex space-x-3">
                    <button onclick="closeModal('confirmModal')" class="btn-secondary flex-1">
                        Cancel
                    </button>
                    <button id="confirmModalYesBtn" class="btn-danger flex-1">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

/**
 * Shows a success modal with the given message
 * @param {string} message - The success message to display
 */
function showSuccessModal(message) {
    showResponseModal('success', 'Success!', message);
}

/**
 * Shows an error modal with the given message
 * @param {string} message - The error message to display
 */
function showErrorModal(message) {
    showResponseModal('error', 'Error!', message);
}

/**
 * Shows a warning modal with the given message
 * @param {string} message - The warning message to display
 */
function showWarningModal(message) {
    showResponseModal('warning', 'Warning!', message);
}

/**
 * Shows an info modal with the given message
 * @param {string} message - The info message to display
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function to execute after modal is closed
 */
function showInfoModal(message, title = 'Information', callback = null) {
    showResponseModal('info', title, message, callback);
}

/**
 * Shows a confirmation modal with the given message
 * @param {string} message - The confirmation message to display
 * @param {function} confirmCallback - Function to execute when confirmed
 * @param {function} cancelCallback - Optional function to execute when canceled
 * @param {string} title - Optional custom title
 */


// Function to show response modal if not already defined
if (typeof showResponseModal !== 'function') {
    function showResponseModal(type, title, message) {
        const modal = document.getElementById('responseModal');
        const iconDiv = document.getElementById('responseIcon');
        const titleElement = document.getElementById('responseTitle');
        const messageElement = document.getElementById('responseMessage');
        
        // Set icon based on type
        if (type === 'success') {
            iconDiv.innerHTML = `
                <div class="bg-green-100 rounded-full p-2 mx-auto inline-block">
                    
                </div>
            `;
        } else if (type === 'error') {
            iconDiv.innerHTML = `
                <div class="bg-red-100 rounded-full p-2 mx-auto inline-block">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            `;
        } else if (type === 'warning') {
            iconDiv.innerHTML = `
                <div class="bg-yellow-100 rounded-full p-2 mx-auto inline-block">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            `;
        }
        
        // Set title and message
        titleElement.textContent = title;
        messageElement.textContent = message;
        
        // Show the modal
        openModal('responseModal');
    }
}

// Function to open modal if not already defined
if (typeof openModal !== 'function') {
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }
}

// Function to close modal if not already defined
if (typeof closeModal !== 'function') {
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
}

// Function to close view modal if not already defined
if (typeof closeViewModal !== 'function') {
    function closeViewModal() {
        closeModal('addOrchardModal');
        closeModal('viewModal');
    }
}
