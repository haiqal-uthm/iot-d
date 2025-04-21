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
        }
    });
}

function saveVibrationCount(orchardId, vibrationCount) {
    fetch("{{ route('save-vibration') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            orchard_id: orchardId,
            vibration_count: parseInt(vibrationCount, 10)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Vibration count saved successfully!');
            resetVibrationCount(orchardId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(console.error);
}

function resetVibrationCount(orchardId) {
    const database = initializeFirebase();
    const sensorRef = database.ref(`sensors/sensor${orchardId}/vibrationCount`);
    sensorRef.set(0);
}

/* ===========================
   Modal Handling Functions
============================== */
function openViewModal(orchardId) {
    console.log("Orchard ID:", orchardId);
    console.log("Orchards Array:", orchards);

    const modal = document.getElementById('viewModal');
    
    // Find the orchard by ID
    const orchard = orchards.find(o => o.id == orchardId);

    if (!orchard) {
        console.error("Orchard not found!");
        return;
    }

    // Update modal content
    document.getElementById('nameOrchard').innerText = orchard.orchardName || 'Orchard Image';
    document.getElementById('numTrees').innerText = orchard.numTree || 'N/A';
    document.getElementById('deviceName').innerText = orchard.device?.name || 'No Device Assigned';
    document.getElementById('durianName').innerText = orchard.durian?.name || 'No Durian Assigned';

    // Set orchard image dynamically (fallback if no image is available)
    const orchardImage = document.getElementById('orchardImage');
    orchardImage.src = orchard.imageUrl || 'https://via.placeholder.com/150';

    // Show the modal
    modal.classList.remove('hidden');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('addOrchardModal').classList.add('hidden');
}

/* ===========================
   Fetch Vibration Count on Page Load
============================== */
document.addEventListener('DOMContentLoaded', () => {
    const database = initializeFirebase();
    
    if (typeof orchards !== "undefined" && Array.isArray(orchards)) {
        orchards.forEach(orchard => {
            fetchVibrationCount(database, orchard.id);
        });
    } else {
        console.error("Orchards array is undefined or not an array.");
    }
    
    // Set up auto-refresh for the vibration log notifications every 30 seconds
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000);
});
