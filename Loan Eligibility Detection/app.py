import numpy as np
from flask import Flask, request, jsonify, render_template
import pickle

# Create flask app
app = Flask(__name__, template_folder='templates')

# Load the pickle model
model = pickle.load(open("model.pkl", "rb"))

# Mapping for property area
property_area_mapping = {'Rural': [1, 0, 0], 'Semiurban': [0, 1, 0], 'Urban': [0, 0, 1]}

@app.route("/")
def Home():
    return render_template("index.html")

@app.route("/predict", methods = ["POST", "GET"])
def predict():
    try:
        # Retrieve form values
        form_values = request.form

               
        # Extract numerical features
        numerical_features = [int(form_values['Dependents']),
                              int(form_values['LoanAmount']),
                              int(form_values['Loan_Amount_Term']),
                              int(form_values['Credit_History']),
                              int(form_values['Married']),
                              int(form_values['Education']),
                              int(form_values['Self_Employed']),
                              int(form_values['Income'])]
        
        property = property_area_mapping[form_values["Property"]]
        data = numerical_features + property
        data_reshaped = np.array(data).reshape(1, -1)

        # Predict using the model
        predicition = model.predict(data_reshaped)

        if predicition == 1:
            show = "Congratulations!!! You are Eligible for a loan"
        else:
            show = "We're Sorry, You are Not Eligible for a loan"
        

        # Render template with prediction
        return render_template("index.html", prediction_text=show)
    
    except Exception as e:
        # Handle errors
        return render_template("index.html", prediction_text="Error: {}".format(str(e)))

if __name__ == "__main__":
    app.run(debug=True)
