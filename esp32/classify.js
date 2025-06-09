const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');
const axios = require('axios');

// --- Firebase credentials ---
const FIREBASE_HOST = "https://iotd-85d25-default-rtdb.asia-southeast1.firebasedatabase.app";
const FIREBASE_AUTH = "AIzaSyD6DqAIqzO1Cz0H9dg_vJQU_zwswDcrZhM";

/**
 * Classify an image using YOLOv5's detect.py.
 */
function classifyImage(
  filename,
  originalDir,
  classifiedDir,
  sensorName = 'sensor1',
  orchardId = 1
) {
  const inputPath = path.join(originalDir, filename);
  const yoloOutputDir = path.join(__dirname, 'yolo_output');

  if (!fs.existsSync(yoloOutputDir)) {
    fs.mkdirSync(yoloOutputDir, { recursive: true });
  }

  const pythonProcess = spawn('python', [
    path.join(__dirname, 'yolov5', 'detect.py'),
    '--source', inputPath,
    '--project', yoloOutputDir,
    '--name', 'result',
    '--exist-ok',
    '--weights', 'best.pt',
    '--img', '224',
    '--conf', '0.5',
    '--save-txt',
    '--save-conf'
  ]);

  pythonProcess.stdout.on('data', (data) => {
    console.log(`YOLOv5 stdout: ${data}`);
  });

  pythonProcess.stderr.on('data', (data) => {
    console.error(`YOLOv5 stderr: ${data}`);
  });

  pythonProcess.on('close', async (code) => {
    if (code !== 0) {
      console.error(`YOLOv5 detect.py exited with code ${code}`);
      return;
    }

    const detectedImgPath = path.join(yoloOutputDir, 'result', filename);
    const classifiedImgPath = path.join(classifiedDir, filename);

    try {
      fs.copyFileSync(detectedImgPath, classifiedImgPath);
      console.log(`Classified image saved: ${classifiedImgPath}`);
    } catch (err) {
      console.error('Error copying classified image:', err.message);
    }

    const labelFile = path.join(yoloOutputDir, 'result', 'labels', filename.replace(/\.[^.]+$/, '.txt'));
    let detectionCount = 0;
    let hasAnimal = false;
    let hasDurian = false;

    try {
      if (fs.existsSync(labelFile)) {
        const lines = fs.readFileSync(labelFile, 'utf-8').trim().split('\n').filter(Boolean);
        detectionCount = lines.filter(l => l.startsWith('0')).length;
        hasDurian = detectionCount > 0;
        hasAnimal = lines.some(l => l.startsWith('1'));
      }
    } catch (e) {
      console.error('Error reading label file:', e.message);
    }

    // Durian logic
    if (hasDurian) {
      // Durian detected: MySQL and Firebase with detectionCount
      await logToMysqlAPI(sensorName, detectionCount, 1, orchardId);
      await saveFirebase(sensorName, detectionCount);
    } else if (hasAnimal) {
      // Animal only: MySQL with count=1, type=2, no Firebase
      await logToMysqlAPI(sensorName, 1, 2, orchardId);
      console.log('Animal detected, Firebase not updated.');
    } else {
      console.log('No durian or animal detected, not updating MySQL or Firebase.');
    }
  });
}

/**
 * Send detection log to Laravel API (MySQL)
 */
async function logToMysqlAPI(sensorName, detectCount, logType = 1, orchardId = 1) {
  const deviceID = 1;

  const params = new URLSearchParams();
  params.append('vibrationCount', String(detectCount));
  params.append('deviceID', String(deviceID));
  params.append('logType', String(logType));
  params.append('orchard_id', String(orchardId));

  try {
    const res = await axios.post(
      'https://iot-d-jopkt.ondigitalocean.app/api/vibration-log',
      params
    );
    console.log(`✅ MySQL API updated for deviceID ${deviceID}:`, res.data);
  } catch (e) {
    console.error(`❌ Error updating MySQL API:`, e.response?.data || e.message);
  }
}

/**
 * Save detection count to Firebase RTDB
 */
async function saveFirebase(sensorName, detectionCount, addToHarvest) {
  try {
    const sensorUrl = `${FIREBASE_HOST}/sensors/${sensorName}.json?auth=${FIREBASE_AUTH}`;
    let prevCount = 0;
    const sensorRes = await axios.get(sensorUrl);
    if (sensorRes.data && typeof sensorRes.data.vibrationCount === 'number') {
      prevCount = sensorRes.data.vibrationCount;
    }
    const newCount = prevCount + detectionCount;
    await axios.patch(sensorUrl, { vibrationCount: newCount });

    if (typeof addToHarvest === 'number') {
      const harvestUrl = `${FIREBASE_HOST}/harvests.json?auth=${FIREBASE_AUTH}`;
      await axios.patch(harvestUrl, { today: addToHarvest });
    }

    console.log(`✅ Firebase updated for ${sensorName}: vibrationCount=${newCount}`);
  } catch (e) {
    console.error(`❌ Error updating Firebase for ${sensorName}:`, e.response?.data || e.message);
  }
}

module.exports = { classifyImage, saveFirebase };
