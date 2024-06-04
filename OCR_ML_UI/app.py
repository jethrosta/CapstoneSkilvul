from flask import Flask, request, render_template, jsonify, send_from_directory
import easyocr
import cv2
import os
import pickle
import numpy as np
import re

app = Flask(__name__)
UPLOAD_FOLDER = 'static/upload'
PROCESSED_FOLDER = 'static/processed'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['PROCESSED_FOLDER'] = PROCESSED_FOLDER

# Initialize EasyOCR reader
reader = easyocr.Reader(['en', 'id'])

# Memuat model dari file pickle
with open('model.pkl', 'rb') as file:
    # Load the model
    model = pickle.load(file)

# Mapping for property area
property_area_mapping = {'rural': [1, 0, 0], 'semiurban': [0, 1, 0], 'urban': [0, 0, 1]}
# Mapping for yes/no fields
yes_no_mapping = {'yes': 1, 'no': 0}


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
        
        extracted_text = process_file(filepath, processed_filepath)
        # Assuming extracted_text is a dictionary with necessary fields
        prediction_result = predict_loan_eligibility(extracted_text)

        return jsonify({'text': extracted_text, 'processed_image': processed_filepath, 'prediction': prediction_result})

def process_file(filepath, processed_filepath):
    img = cv2.imread(filepath)
    result = reader.readtext(img)
    extracted_data = {}  # Initialize dictionary to store extracted data
    
    for detection in result:
        top_left = tuple([int(val) for val in detection[0][0]])
        bottom_right = tuple([int(val) for val in detection[0][2]])
        text = detection[1]
        font = cv2.FONT_HERSHEY_SIMPLEX
        img = cv2.rectangle(img, top_left, bottom_right, (0,255,0), 3)
        img = cv2.putText(img, text, top_left, font, .7, (0, 0, 255), 2, cv2.LINE_AA)
        
        # Extract fields and their values
        for field_name in ['Dependents', 'Loan Amount', 'Loan Amount Term', 'Credit History', 
                           'Married', 'Education', 'Self Employed', 'Income', 'Property']:
            extracted_data[field_name] = extract_field(result, field_name)

    cv2.imwrite(processed_filepath, img)
    
    return extracted_data



def extract_field(result, field_name):
    """
    Extract specific field values from OCR result based on field name.
    
    :param result: OCR result list.
    :param field_name: The field name to extract.
    :return: Extracted field value as a string.
    """
    # Convert field name to lowercase for case-insensitive matching
    field_name_lower = field_name.lower()
    
    
    # Iterate through the OCR result to find the field
    for item in result:
        text = item[1].lower()  # Extracted text
        if field_name_lower in text:
            # Extract the part of the string that matches the field name
            match = re.search(rf'{field_name_lower}:\s*(\S+)', text)  
            if match:
                return match.group(1)  # Return the extracted value
    
    # Return default value if the field is not found in the OCR result
    return '0'



def predict_loan_eligibility(extracted_data):
    try:
        numerical_features = [
            int(extracted_data['Dependents']),
            int(extracted_data['Loan Amount']),
            int(extracted_data['Loan Amount Term']),
            int(extracted_data['Income'])
        ]

        selfemployed = [yes_no_mapping[extracted_data['Self Employed']]]
        education = [yes_no_mapping[extracted_data['Education']]]
        married = [yes_no_mapping[extracted_data['Married']]]
        credit = [yes_no_mapping[extracted_data['Credit History']]]
        property = property_area_mapping[extracted_data["Property"]]
        data = numerical_features + property + credit + married + education + selfemployed
        data_reshaped = np.array(data).reshape(1, -1)
        prediction = model.predict(data_reshaped)

        if prediction == 1:
            return "Congratulations!!! You are Eligible for a loan"
        else:
            return "We're Sorry, You are Not Eligible for a loan"
    
    except Exception as e:
        return "Error: {}".format(str(e))

@app.route('/static/<path:filename>')
def download_file(filename):
    return send_from_directory('static', filename)

if __name__ == '__main__':
    os.makedirs(UPLOAD_FOLDER, exist_ok=True)
    os.makedirs(PROCESSED_FOLDER, exist_ok=True)
    app.run(debug=True)
