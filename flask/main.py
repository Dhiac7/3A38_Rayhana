from flask import Flask, jsonify
from app1 import app1
from app2 import app2
from flask_cors import CORS

app = Flask(__name__)


# Enregistrer les applications (Blueprints)
app.register_blueprint(app1, url_prefix='/model1')
app.register_blueprint(app2, url_prefix='/model2')

# Ajouter une route `/predict` personnalisée
@app.route('/predict', methods=['POST'])
def predict():
    return jsonify({'message': 'Cette route est disponible sans préfixe.'})

CORS(app)  # Autorise toutes les origines


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
