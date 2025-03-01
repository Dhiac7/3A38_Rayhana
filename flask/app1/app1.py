from flask import Blueprint,Flask, request, jsonify, render_template
import numpy as np
import cv2
from tensorflow.keras.models import load_model
import os

app1 = Blueprint('app1', __name__, template_folder='templates')  # Déclarer un Blueprint

# Charger le modèle

model_path = os.path.join(os.path.dirname(__file__), "fruit_vegetable_classifier.h5")
model1 = load_model(model_path)

class_labels_model1 = ['Apple Healthy', 'Apple Rotten', 'Banana Healthy', 'Banana Rotten',
                       'Bellpepper Healthy', 'Bellpepper Rotten']

def predict_image(image_path):
    image = cv2.imread(image_path)
    image = cv2.resize(image, (128, 128))
    image = np.expand_dims(image, axis=0) / 255.0
    prediction = model1.predict(image)
    predicted_class = class_labels_model1[np.argmax(prediction)]
    return predicted_class


@app1.route('/')
def home():
    return render_template("index.html")  

@app1.route('/predict_model1', methods=['POST'])
def predict_model1():
    # Vérifie si un fichier a été envoyé
    if 'file' not in request.files:
        return jsonify({'error': 'Aucun fichier envoyé'}), 400  # Code HTTP 400 pour une mauvaise requête

    file = request.files['file']

    # Vérifie si un fichier a été sélectionné
    if file.filename == '':
        return jsonify({'error': 'Aucun fichier sélectionné'}), 400

    # Vérifie que le fichier est une image (extension valide)
    allowed_extensions = {'jpg', 'jpeg', 'png'}
    if '.' not in file.filename or file.filename.split('.')[-1].lower() not in allowed_extensions:
        return jsonify({'error': 'Format de fichier non supporté. Utilisez JPG, JPEG ou PNG.'}), 400

    # Crée un dossier 'uploads' s'il n'existe pas
    upload_folder = 'uploads'
    if not os.path.exists(upload_folder):
        os.makedirs(upload_folder)

    # Enregistre le fichier dans le dossier 'uploads'
    file_path = os.path.join(upload_folder, file.filename)
    file.save(file_path)

    # Vérifie que le fichier a bien été enregistré
    if not os.path.exists(file_path):
        return jsonify({'error': 'Erreur lors de l\'enregistrement du fichier'}), 500

    # Appelle la fonction de prédiction
    try:
        predicted_class = predict_image(file_path)
    except Exception as e:
        # En cas d'erreur lors de la prédiction
        return jsonify({'error': f'Erreur lors de la prédiction : {str(e)}'}), 500

    # Supprime le fichier après la prédiction (optionnel)
    os.remove(file_path)

    # Retourne le résultat de la prédiction
    return jsonify({'prediction': predicted_class}), 200
