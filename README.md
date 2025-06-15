# Credit Flash

**Credit Flash** is a final capstone project that combines **Optical Character Recognition (OCR)** with **Loan Fraud Detection** to provide a seamless and secure credit eligibility checking platform. Leveraging **IBM Cloud services**, powerful **Python machine learning libraries**, and **IBM servers**, this system helps financial institutions detect fraudulent loan applications quickly and accurately.

---

## Project Overview

The goal of Credit Flash is to automate the verification and fraud detection process in loan applications by:

- Extracting text data from scanned or photographed documents using OCR.
- Applying machine learning models to analyze application data for potential fraud.
- Providing real-time fraud alerts and eligibility status.
- Deploying the solution on reliable IBM Cloud infrastructure for scalability and security.

---

## Key Features

- **OCR Integration:** Extracts relevant data (e.g., identity documents, income proofs) using IBM Watson Visual Recognition or IBM Document Conversion services.
- **Fraud Detection Model:** Utilizes Python ML libraries (such as scikit-learn, XGBoost, TensorFlow) to detect anomalies and classify applications as legitimate or fraudulent.
- **Cloud Deployment:** Runs on IBM Cloud servers with containerized microservices for high availability.
- **Real-time Analysis:** Enables near-instant feedback on loan application status.
- **Secure Data Handling:** Complies with data privacy standards using IBM Cloud security features.

---

## Technologies Used

<div align="center">
  
| Technology               | Purpose                                         |
|-------------------------|------------------------------------------------|
| IBM Watson Visual Recognition | OCR and document data extraction            |
| IBM Cloud Kubernetes    | Deployment and scaling of backend services      |
| Python                  | Machine learning model development and data processing |
| scikit-learn, XGBoost, TensorFlow | Building and training fraud detection models    |
| Flask / FastAPI         | API endpoints for interaction between frontend and backend |
| IBM Cloud Object Storage| Storing scanned documents securely              |
| IBM Cloud Functions     | Serverless computing for lightweight tasks      |
</div>

---

## System Architecture

1. **User uploads loan documents** via web or mobile interface.  
2. **OCR service (IBM Watson)** processes documents to extract text and numeric data.  
3. Extracted data is sent to **fraud detection ML model** hosted on IBM Cloud.  
4. The ML model analyzes data and classifies the loan application.  
5. Results and alerts are sent back to the user interface for display.  
6. Application logs and data are securely stored for auditing.

---

## Getting Started

### Prerequisites

- IBM Cloud account with access to Watson services and Kubernetes cluster.  
- Python 3.8+ environment with necessary libraries installed.  
- API keys and credentials for IBM services.

### Installation & Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/jethrosta/credit-flash.git
   cd credit-flash
2. Install Python dependencies:
   ```bash
   pip install -r requirements.txt
3. Configure IBM Cloud credentials in .env file:
   ```bash
   IBM_WATSON_APIKEY=your_api_key
   IBM_WATSON_URL=your_service_url
   IBM_CLOUD_OBJECT_STORAGE=your_cos_credentials
4. Deploy backend services to IBM Kubernetes or use IBM Cloud Functions.
5. Run the Flask/FastAPI backend locally for testing:
   ```bash
    python app.py
6. Access the frontend to upload documents and view loan eligibility results.

## Future Improvements
- Integrate advanced NLP techniques for better document understanding.
- Add multi-factor authentication to secure the platform.
- Improve model accuracy with more extensive and diverse datasets.
- Build a user-friendly dashboard for monitoring loan application statuses.
- Deploy mobile app for easier access by users.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Contact
Feivel Jethro Ezhekiel <br>
Email: feiveljethroezhekiel@gmail.com <br>
GitHub: https://github.com/jethrosta <br>

Thank you for checking out Credit Flash! Feel free to contribute or reach out for collaborations. Let me know if you want me to help create the frontend HTML/CSS or deployment instructions!
