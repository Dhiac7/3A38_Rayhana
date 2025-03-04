from flask import Blueprint, request, jsonify
import tensorflow as tf
import numpy as np
from PIL import Image
import io
import os
from tensorflow.keras.models import load_model

# Créer un Blueprint pour app3
app3 = Blueprint('app3', __name__)

# Charger le modèle une seule fois lors du démarrage
model_path = os.path.join(os.path.dirname(__file__), "trained_model.h5")
model3 = load_model(model_path)

# Liste des classes (à adapter selon ton modèle)
class_names = [
    'Apple___Apple_scab', 'Apple___Black_rot', 'Apple___Cedar_apple_rust', 'Apple___healthy',
    'Blueberry___healthy', 'Cherry_(including_sour)___Powdery_mildew', 'Cherry_(including_sour)___healthy',
    'Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot', 'Corn_(maize)___Common_rust_', 
    'Corn_(maize)___Northern_Leaf_Blight', 'Corn_(maize)___healthy', 'Grape___Black_rot',
    'Grape___Esca_(Black_Measles)', 'Grape___Leaf_blight_(Isariopsis_Leaf_Spot)', 'Grape___healthy',
    'Orange___Haunglongbing_(Citrus_greening)', 'Peach___Bacterial_spot', 'Peach___healthy',
    'Pepper,_bell___Bacterial_spot', 'Pepper,_bell___healthy', 'Potato___Early_blight', 
    'Potato___Late_blight', 'Potato___healthy', 'Raspberry___healthy', 'Soybean___healthy',
    'Squash___Powdery_mildew', 'Strawberry___Leaf_scorch', 'Strawberry___healthy', 
    'Tomato___Bacterial_spot', 'Tomato___Early_blight', 'Tomato___Late_blight', 
    'Tomato___Leaf_Mold', 'Tomato___Septoria_leaf_spot', 'Tomato___Spider_mites Two-spotted_spider_mite',
    'Tomato___Target_Spot', 'Tomato___Tomato_Yellow_Leaf_Curl_Virus', 'Tomato___Tomato_mosaic_virus',
    'Tomato___healthy'
]

def model_prediction(image_bytes):
    try:
        # Charger l'image depuis les bytes et la redimensionner
        image = Image.open(io.BytesIO(image_bytes))
        image = image.resize((128, 128))  # Redimensionner à la taille attendue par le modèle
        input_arr = np.array(image)  # Convertir en tableau numpy
        input_arr = input_arr / 255.0  # Normaliser les valeurs des pixels (si nécessaire)
        input_arr = np.expand_dims(input_arr, axis=0)  # Créer un batch d'une seule image

        # Faire la prédiction
        prediction = model3.predict(input_arr)
        result_index = int(np.argmax(prediction))  # Obtenir l'indice de la classe prédite
        return result_index
    except Exception as e:
        print(f"Erreur lors de la prédiction : {e}")
        return None

@app3.route('/predict', methods=['POST'])
def predict():
    if 'file' not in request.files:
        return jsonify({'error': 'Aucun fichier fourni'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'Aucun fichier sélectionné'}), 400

    try:
        image_bytes = file.read()
        result_index = model_prediction(image_bytes)

        if result_index is None:
            return jsonify({'error': 'Erreur lors de la prédiction'}), 500

        result = class_names[result_index]
        return jsonify({'prediction': result})
    except Exception as e:
        return jsonify({'error': f'Erreur lors du traitement du fichier : {str(e)}'}), 500