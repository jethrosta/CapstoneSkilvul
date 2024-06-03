from flask import Flask, request, render_template, jsonify, send_from_directory
import easyocr
import cv2
import os
import pickle
import numpy as np

app = Flask(__name__)
UPLOAD_FOLDER = 'static/upload'
PROCESSED_FOLDER = 'static/processed'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['PROCESSED_FOLDER'] = PROCESSED_FOLDER

# Initialize EasyOCR reader
reader = easyocr.Reader(['en', 'id'])

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/upload', methods=['POST'])
def upload():
    if 'file' not in request.files:
        return jsonify({'error': 'No file part'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    if file:
        filepath = os.path.join(app.config['UPLOAD_FOLDER'], file.filename)
        processed_filepath = os.path.join(app.config['PROCESSED_FOLDER'], file.filename)
        file.save(filepath)
        
        result = process_file(filepath, processed_filepath)
        return jsonify({'text': result, 'processed_image': processed_filepath})

def process_file(filepath, processed_filepath):
    img = cv2.imread(filepath)
    result = reader.readtext(img)
    for detection in result:
        top_left = tuple([int(val) for val in detection[0][0]])
        bottom_right = tuple([int(val) for val in detection[0][2]])
        text = detection[1]
        font = cv2.FONT_HERSHEY_SIMPLEX
        img = cv2.rectangle(img, top_left, bottom_right, (0,255,0), 3)
        img = cv2.putText(img, text, top_left, font, .7, (0, 0, 255), 2, cv2.LINE_AA)
    
    cv2.imwrite(processed_filepath, img)
    return ' '.join([item[1] for item in result])

@app.route('/static/<path:filename>')
def download_file(filename):
    return send_from_directory('static', filename)

if __name__ == '__main__':
    os.makedirs(UPLOAD_FOLDER, exist_ok=True)
    os.makedirs(PROCESSED_FOLDER, exist_ok=True)
    app.run(debug=True)
