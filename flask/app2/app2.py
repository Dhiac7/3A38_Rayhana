from flask import Blueprint, request, jsonify, render_template
import numpy as np
import os
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import load_img, img_to_array

app2 = Blueprint('app2', __name__, template_folder='templates')  # Déclarer un Blueprint

# Charger le modèle
model_path = os.path.join(os.path.dirname(__file__), "FruitVegModel_MobileNetV2.h5")
model2 = load_model(model_path)

# Liste des classes et des calories
class_labels_model2 = [
    'apple', 'banana', 'beetroot', 'bell pepper', 'cabbage', 'capsicum', 'carrot', 
    'cauliflower', 'chilli pepper', 'corn', 'cucumber', 'eggplant', 'garlic', 'ginger', 
    'grapes', 'jalepeno', 'kiwi', 'lemon', 'lettuce', 'mango', 'onion', 'orange', 
    'paprika', 'pear', 'peas', 'pineapple', 'pomegranate', 'potato', 'raddish', 
    'soy beans', 'spinach', 'sweetcorn', 'sweetpotato', 'tomato', 'turnip', 'watermelon'
]

calories = {
    'apple': 52, 'banana': 96, 'beetroot': 43, 'bell pepper': 20, 'cabbage': 25,
    'capsicum': 20, 'carrot': 41, 'cauliflower': 25, 'chilli pepper': 40, 'corn': 86,
    'cucumber': 15, 'eggplant': 25, 'garlic': 149, 'ginger': 80, 'grapes': 69,
    'jalepeno': 29, 'kiwi': 61, 'lemon': 29, 'lettuce': 15, 'mango': 60, 'onion': 40,
    'orange': 47, 'paprika': 27, 'pear': 57, 'peas': 81, 'pineapple': 50,
    'pomegranate': 83, 'potato': 77, 'raddish': 16, 'soy beans': 173, 'spinach': 23,
    'sweetcorn': 86, 'sweetpotato': 86, 'tomato': 18, 'turnip': 28, 'watermelon': 30
}

def predict_image_model2(image_path):
    """
    Prédit la classe et les calories d'une image.
    """
    try:
        # Charger et prétraiter l'image
        image = load_img(image_path, target_size=(224, 224))
        image = img_to_array(image) / 255.0
        image = np.expand_dims(image, axis=0)

        # Faire la prédiction
        predictions = model2.predict(image)
        predicted_class = class_labels_model2[np.argmax(predictions)]
        calories_per_100g = calories.get(predicted_class, "N/A")

        return predicted_class, calories_per_100g
    except Exception as e:
        print(f"Erreur lors de la prédiction : {e}")
        return None, None

@app2.route('/')
def home():
    """
    Route pour afficher la page d'accueil.
    """
    return render_template("index2.html")

@app2.route('/predict_model2', methods=['POST'])
def predict_model2():
    """
    Route pour gérer les prédictions.
    """
    # Vérifier si un fichier a été envoyé
    if 'file' not in request.files:
        return jsonify({'error': 'Aucun fichier envoyé'}), 400

    file = request.files['file']

    # Vérifier si un fichier a été sélectionné
    if file.filename == '':
        return jsonify({'error': 'Aucun fichier sélectionné'}), 400

    # Vérifier le format du fichier
    allowed_extensions = {'jpg', 'jpeg', 'png'}
    if '.' not in file.filename or file.filename.split('.')[-1].lower() not in allowed_extensions:
        return jsonify({'error': 'Format de fichier non supporté. Utilisez JPG, JPEG ou PNG.'}), 400

    # Créer le dossier 'uploads' s'il n'existe pas
    upload_folder = 'uploads'
    if not os.path.exists(upload_folder):
        os.makedirs(upload_folder)

    # Enregistrer le fichier
    file_path = os.path.join(upload_folder, file.filename)
    file.save(file_path)

    # Vérifier que le fichier a bien été enregistré
    if not os.path.exists(file_path):
        return jsonify({'error': 'Erreur lors de l\'enregistrement du fichier'}), 500

    # Faire la prédiction
    predicted_class, calories_per_100g = predict_image_model2(file_path)

    # Supprimer le fichier après la prédiction
    os.remove(file_path)

    # Vérifier si la prédiction a réussi
    if predicted_class is None or calories_per_100g is None:
        return jsonify({'error': 'Erreur lors de la prédiction'}), 500

    # Retourner le résultat
    return jsonify({
        'prediction': predicted_class,
        'calories_per_100g': calories_per_100g
    }), 200