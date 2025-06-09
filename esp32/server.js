const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { classifyImage } = require('./classify');
const app = express();
const port = 3000;

// Create directories if they don't exist
const originalDir = path.join(__dirname, 'uploads', 'original');
const classifiedDir = path.join(__dirname, 'uploads', 'classified');

if (!fs.existsSync(path.join(__dirname, 'uploads'))) {
  fs.mkdirSync(path.join(__dirname, 'uploads'));
}
if (!fs.existsSync(originalDir)) {
  fs.mkdirSync(originalDir);
}
if (!fs.existsSync(classifiedDir)) {
  fs.mkdirSync(classifiedDir);
}

// Configure storage
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, 'uploads/original/')
  },
  filename: function (req, file, cb) {
    // Save with timestamp to avoid overwriting
    const timestamp = new Date().toISOString().replace(/:/g, '-');
    cb(null, `image_${timestamp}.jpg`);
  }
});

const upload = multer({ storage: storage });

// Middleware to parse raw binary data
app.use(express.raw({ type: 'image/jpeg', limit: '10mb' }));

// Serve static files from the uploads directories
app.use('/images/original', express.static('uploads/original'));
app.use('/images/classified', express.static('uploads/classified'));

// HTML page to view images
app.get('/', (req, res) => {
  res.send(`
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
          // Load original images
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
            
          // Load classified images
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
  `);
});

// Endpoint to list images from a specific directory
app.get('/list-images/:type', (req, res) => {
  const type = req.params.type;
  const dir = type === 'original' ? originalDir : classifiedDir;
  
  fs.readdir(dir, (err, files) => {
    if (err) {
      return res.status(500).json({ error: `Failed to list ${type} images` });
    }
    // Filter only jpg files
    const images = files.filter(file => file.endsWith('.jpg'));
    // Sort by creation time (newest first)
    images.sort((a, b) => {
      return fs.statSync(path.join(dir, b)).mtime.getTime() - 
             fs.statSync(path.join(dir, a)).mtime.getTime();
    });
    res.json(images);
  });
});

// Process unclassified images
function processUnclassifiedImages() {
  fs.readdir(originalDir, (err, files) => {
    if (err) {
      console.error('Error reading original directory:', err);
      return;
    }
    
    // Get list of classified images
    fs.readdir(classifiedDir, (err, classifiedFiles) => {
      if (err) {
        console.error('Error reading classified directory:', err);
        return;
      }
      
      // Find images that haven't been classified yet
      const unclassifiedImages = files.filter(file => {
        return file.endsWith('.jpg') && !classifiedFiles.includes(file);
      });
      
      // Process each unclassified image
      unclassifiedImages.forEach(filename => {
        classifyImage(filename, originalDir, classifiedDir);
      });
    });
  });
}

// Handle image upload from ESP32-CAM
app.post('/upload', (req, res) => {
    const timestamp = new Date().toISOString().replace(/:/g, '-');
    const filename = `image_${timestamp}.jpg`;
    const filepath = path.join(originalDir, filename);
    
    fs.writeFile(filepath, req.body, (err) => {
      if (err) {
        console.error('Error saving image:', err);
        return res.status(500).send('Error saving image');
      }
      console.log(`Image saved: ${filename}`);
      
      // Classify the newly uploaded image
      classifyImage(filename, originalDir, classifiedDir);
      
      res.status(200).send('Image uploaded successfully');
    });
});

// Run the image processor periodically to catch any missed images
setInterval(processUnclassifiedImages, 30000); // Check every 30 seconds

app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://localhost:${port}`);
  console.log(`Make sure to update the ESP32 code with your computer's IP address`);
  
  // Initial processing of any existing images
  processUnclassifiedImages();
});