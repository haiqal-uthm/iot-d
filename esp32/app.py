import os
import threading
import time
import subprocess
import shutil
import requests
from PIL import Image
import io
from flask import Flask, request, send_from_directory, jsonify, render_template_string

# --- Configuration ---
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.join(BASE_DIR, 'uploads')
ORIGINAL_DIR = os.path.join(UPLOAD_FOLDER, 'original')
CLASSIFIED_DIR = os.path.join(UPLOAD_FOLDER, 'classified')

YOLOV5_DIR = os.path.join(BASE_DIR, 'yolov5')
YOLO_WEIGHTS = os.path.join(BASE_DIR, 'best.pt')
YOLO_OUTPUT_DIR = os.path.join(BASE_DIR, 'yolo_output')

FIREBASE_HOST = "https://iotd-85d25-default-rtdb.asia-southeast1.firebasedatabase.app"
FIREBASE_AUTH = "AIzaSyD6DqAIqzO1Cz0H9dg_vJQU_zwswDcrZhM"

# --- Flask App Setup ---
app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['MAX_CONTENT_LENGTH'] = 10 * 1024 * 1024  # 10 MB

# --- Ensure directories exist ---
os.makedirs(ORIGINAL_DIR, exist_ok=True)
os.makedirs(CLASSIFIED_DIR, exist_ok=True)
os.makedirs(YOLO_OUTPUT_DIR, exist_ok=True)

# --- Utility Functions for Image Classification ---
def classify_image(
    filename,
    original_dir=ORIGINAL_DIR,
    classified_dir=CLASSIFIED_DIR,
    sensor_name='sensor1',
    orchard_id=1
):
    input_path = os.path.join(original_dir, filename)
    output_dir = YOLO_OUTPUT_DIR

    # 1. Run YOLOv5 detect.py synchronously
    yolo_cmd = [
        'python', os.path.join(YOLOV5_DIR, 'detect.py'),
        '--source', input_path,
        '--project', output_dir,
        '--name', 'result',
        '--exist-ok',
        '--weights', YOLO_WEIGHTS,
        '--img', '224',
        '--conf', '0.5',
        '--save-txt',
        '--save-conf'
    ]
    proc = subprocess.run(yolo_cmd, capture_output=True)
    if proc.returncode != 0:
        print('YOLOv5 error:', proc.stderr.decode())
        return

    # 2. Copy the detected image to classified_dir
    detected_img_path = os.path.join(output_dir, 'result', filename)
    classified_img_path = os.path.join(classified_dir, filename)
    try:
        shutil.copyfile(detected_img_path, classified_img_path)
        print(f"Classified image saved: {classified_img_path}")
    except Exception as e:
        print("Error copying classified image:", e)

    # 3. Parse label file
    label_file = os.path.join(output_dir, 'result', 'labels', filename.rsplit('.', 1)[0] + '.txt')
    detection_count = 0
    has_animal = False
    has_durian = False
    try:
        if os.path.exists(label_file):
            with open(label_file, 'r') as f:
                lines = [line.strip() for line in f if line.strip()]
            detection_count = sum(1 for l in lines if l.startswith('0'))
            has_durian = detection_count > 0
            has_animal = any(l.startswith('1') for l in lines)
    except Exception as e:
        print("Error reading label file:", e)

    # 4. Logging logic for durian/animal
    if has_durian:
        log_to_mysql_api(sensor_name, detection_count, 1, orchard_id)
        save_firebase(sensor_name, detection_count)
    elif has_animal:
        log_to_mysql_api(sensor_name, 1, 2, orchard_id)
        print("Animal detected, Firebase not updated.")
    else:
        print("No durian or animal detected, not updating MySQL or Firebase.")

def log_to_mysql_api(sensor_name, detect_count, log_type=1, orchard_id=1):
    device_id = 1
    params = {
        'vibrationCount': str(detect_count),
        'deviceID': str(device_id),
        'logType': str(log_type),
        'orchard_id': str(orchard_id)
    }
    try:
        res = requests.post(
            'https://iot-d-jopkt.ondigitalocean.app/api/vibration-log',
            data=params
        )
        print(f"✅ MySQL API updated for deviceID {device_id}: {res.text}")
    except Exception as e:
        print(f"❌ Error updating MySQL API:", e)

def save_firebase(sensor_name, detection_count, add_to_harvest=None):
    try:
        sensor_url = f"{FIREBASE_HOST}/sensors/{sensor_name}.json?auth={FIREBASE_AUTH}"
        prev_count = 0
        sensor_res = requests.get(sensor_url)
        if sensor_res.ok and sensor_res.json() and isinstance(sensor_res.json().get('vibrationCount'), int):
            prev_count = sensor_res.json()['vibrationCount']
        new_count = prev_count + detection_count
        requests.patch(sensor_url, json={'vibrationCount': new_count})
        if add_to_harvest is not None:
            harvest_url = f"{FIREBASE_HOST}/harvests.json?auth={FIREBASE_AUTH}"
            requests.patch(harvest_url, json={'today': add_to_harvest})
        print(f"✅ Firebase updated for {sensor_name}: vibrationCount={new_count}")
    except Exception as e:
        print(f"❌ Error updating Firebase for {sensor_name}:", e)

def process_unclassified_images():
    while True:
        try:
            orig_files = set(f for f in os.listdir(ORIGINAL_DIR) if f.endswith('.jpg'))
            class_files = set(f for f in os.listdir(CLASSIFIED_DIR) if f.endswith('.jpg'))
            unclassified = orig_files - class_files
            for filename in unclassified:
                classify_image(filename)
        except Exception as e:
            print("Error in periodic processing:", e)
        time.sleep(30)

# --- Flask Routes ---
@app.route('/images/<imgtype>/<filename>')
def serve_image(imgtype, filename):
    folder = ORIGINAL_DIR if imgtype == "original" else CLASSIFIED_DIR
    return send_from_directory(folder, filename)

@app.route('/list-images/<imgtype>')
def list_images(imgtype):
    folder = ORIGINAL_DIR if imgtype == "original" else CLASSIFIED_DIR
    try:
        files = [f for f in os.listdir(folder) if f.endswith('.jpg')]
        files.sort(key=lambda x: os.path.getmtime(os.path.join(folder, x)), reverse=True)
        return jsonify(files)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/', methods=['GET'])
def index():
    return render_template_string('''
    <!DOCTYPE html>
    <html>
    <head>
      <title>ESP32-CAM Images</title>
      <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        .image-container { display: flex; flex-wrap: wrap; }
        .image-card { margin: 10px; border: 1px solid #ddd; padding: 10px; }
        img { max-width: 320px; max-height: 240px; }
        .refresh { margin-bottom: 20px; }
        .image-section { margin-bottom: 30px; }
      </style>
      <script>
        function loadImages() {
          fetch('/list-images/original')
            .then(response => response.json())
            .then(images => {
              const container = document.getElementById('original-images');
              container.innerHTML = '';
              images.forEach(image => {
                const card = document.createElement('div');
                card.className = 'image-card';
                const img = document.createElement('img');
                img.src = '/images/original/' + image;
                const caption = document.createElement('p');
                caption.textContent = image;
                card.appendChild(img);
                card.appendChild(caption);
                container.appendChild(card);
              });
            });
          fetch('/list-images/classified')
            .then(response => response.json())
            .then(images => {
              const container = document.getElementById('classified-images');
              container.innerHTML = '';
              images.forEach(image => {
                const card = document.createElement('div');
                card.className = 'image-card';
                const img = document.createElement('img');
                img.src = '/images/classified/' + image;
                const caption = document.createElement('p');
                caption.textContent = image;
                card.appendChild(img);
                card.appendChild(caption);
                container.appendChild(card);
              });
            });
        }
      </script>
    </head>
    <body onload="loadImages()">
      <h1>ESP32-CAM Captured Images</h1>
      <div class="refresh">
        <button onclick="loadImages()">Refresh Images</button>
      </div>
      <div class="image-section">
        <h2>Original Images</h2>
        <div id="original-images" class="image-container">
          Loading images...
        </div>
      </div>
      <div class="image-section">
        <h2>Classified Images</h2>
        <div id="classified-images" class="image-container">
          Loading images...
        </div>
      </div>
    </body>
    </html>
    ''')

@app.route('/upload', methods=['POST'])
def upload():
    timestamp = time.strftime("%Y-%m-%dT%H-%M-%S")
    filename = f"image_{timestamp}.jpg"
    filepath = os.path.join(ORIGINAL_DIR, filename)
    try:
        # Load the image from the POSTed data
        image = Image.open(io.BytesIO(request.data))
        # Convert to RGB if not already (JPEG does not support alpha channel)
        if image.mode != 'RGB':
            image = image.convert('RGB')
        # Resave as JPEG to ensure format
        image.save(filepath, format='JPEG', quality=95)

        # Classify asynchronously
        threading.Thread(target=classify_image, args=(filename,)).start()
        return "Image uploaded and re-saved as JPEG successfully", 200
    except Exception as e:
        return f"Error processing image: {str(e)}", 500

# --- Background Thread for Periodic Processing ---
def start_background_processor():
    t = threading.Thread(target=process_unclassified_images, daemon=True)
    t.start()

if __name__ == '__main__':
    start_background_processor()
    app.run(host='0.0.0.0', port=8080)