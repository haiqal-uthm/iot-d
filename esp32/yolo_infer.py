import sys
import torch
import pathlib
import platform

if platform.system() == 'Windows':
    pathlib.PosixPath = pathlib.WindowsPath

MODEL_PATH = pathlib.Path('C:/xampp/htdocs/esp32/last.pt')
CONFIDENCE_THRESHOLD = 0.25

model = torch.hub.load('yolov5', 'custom', path=str(MODEL_PATH), source='local')
model.conf = CONFIDENCE_THRESHOLD

def detect_and_save(image_path, output_dir='output'):
    # Match detect.py: set device, preprocess, etc.
    import os
    os.makedirs(output_dir, exist_ok=True)
    results = model(image_path)
    results.save(save_dir=output_dir)
    print(f"Saved image(s) with detections to: {output_dir}")

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python yolo_infer_like_detect.py <image_path> [output_dir]")
        sys.exit(1)
    image_path = sys.argv[1]
    output_dir = sys.argv[2] if len(sys.argv) > 2 else 'output'
    detect_and_save(image_path, output_dir)