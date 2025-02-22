import streamlit as st
import tensorflow as tf
import numpy as np
from PIL import Image

# Charger le modèle une seule fois pour éviter les ralentissements
@st.cache_resource
def load_model():
    model_path = 'trained_model.h5'
    try:
        model = tf.keras.models.load_model(model_path)
        st.success("✅ Modèle chargé avec succès !")
        return model
    except Exception as e:
        st.error(f"❌ Erreur lors du chargement du modèle : {str(e)}")
        return None

model = load_model()

# Liste des classes
class_names = [
    'Apple___Apple_scab', 'Apple___Black_rot', 'Apple___Cedar_apple_rust', 'Apple___healthy',
    'Blueberry___healthy', 'Cherry_(including_sour)___Powdery_mildew', 'Cherry_(including_sour)___healthy',
    'Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot', 'Corn_(maize)___Common_rust_', 'Corn_(maize)___Northern_Leaf_Blight',
    'Corn_(maize)___healthy', 'Grape___Black_rot', 'Grape___Esca_(Black_Measles)', 'Grape___Leaf_blight_(Isariopsis_Leaf_Spot)',
    'Grape___healthy', 'Orange___Haunglongbing_(Citrus_greening)', 'Peach___Bacterial_spot', 'Peach___healthy',
    'Pepper,_bell___Bacterial_spot', 'Pepper,_bell___healthy', 'Potato___Early_blight', 'Potato___Late_blight',
    'Potato___healthy', 'Raspberry___healthy', 'Soybean___healthy', 'Squash___Powdery_mildew',
    'Strawberry___Leaf_scorch', 'Strawberry___healthy', 'Tomato___Bacterial_spot', 'Tomato___Early_blight',
    'Tomato___Late_blight', 'Tomato___Leaf_Mold', 'Tomato___Septoria_leaf_spot',
    'Tomato___Spider_mites Two-spotted_spider_mite', 'Tomato___Target_Spot', 'Tomato___Tomato_Yellow_Leaf_Curl_Virus',
    'Tomato___Tomato_mosaic_virus', 'Tomato___healthy'
]

# Fonction pour la prédiction
def model_prediction(image):
    image = image.resize((128, 128))  # Redimensionner l'image
    image = np.array(image) / 255.0   # Normaliser l'image
    image = np.expand_dims(image, axis=0)  # Ajouter une dimension batch
    
    prediction = model.predict(image)
    result_index = int(np.argmax(prediction))
    return result_index

# -------------------------- Streamlit UI --------------------------

# Sidebar
st.sidebar.title("Dashboard")
app_mode = st.sidebar.radio("Navigation", ["🏠 Home", "ℹ️ About", "🔍 Disease Recognition"])

# 🏠 Home Page
if app_mode == "🏠 Home":
    st.title("🌿 PLANT DISEASE RECOGNITION SYSTEM 🌱")
    st.image("home_page.jpeg", use_column_width=True)
    st.markdown("""
    ## Bienvenue ! 👋  
    Cette application vous aide à identifier les maladies des plantes en téléchargeant simplement une image.  

    **Comment ça marche ?**  
    1️⃣ Allez à la section **Disease Recognition**.  
    2️⃣ Téléchargez une image d'une feuille suspecte.  
    3️⃣ Le modèle identifiera la maladie et affichera les résultats.  

    🔎 **Essayez maintenant en allant dans la section `Disease Recognition` !**
    """)

# ℹ️ About Page
elif app_mode == "ℹ️ About":
    st.title("ℹ️ About")
    st.markdown("""
    ### 📊 À propos du Dataset  
    Ce dataset contient environ **87K images** d'**arbres fruitiers et légumes** touchés par **38 maladies différentes**.  

    - **70,295 images** pour l'entraînement  
    - **17,572 images** pour la validation  
    - **33 images** pour les tests  
    """)

# 🔍 Disease Recognition Page
elif app_mode == "🔍 Disease Recognition":
    st.title("🔍 Disease Recognition")

    # Upload de l'image
    uploaded_file = st.file_uploader("📤 Téléchargez une image de feuille :", type=["jpg", "jpeg", "png"])

    if uploaded_file:
        image = Image.open(uploaded_file)
        st.image(image, caption="🌱 Image chargée avec succès", use_column_width=True)

        if st.button("🔍 Prédire"):
            with st.spinner("⏳ Analyse en cours..."):
                if model:
                    result_index = model_prediction(image)
                    prediction = class_names[result_index]
                    st.success(f"✅ Résultat : {prediction}")
                else:
                    st.error("🚨 Modèle non disponible. Vérifiez le fichier `trained_model.h5`.")

