# Agri-Connect Platform

## Overview
This project was developed as part of the coursework for 3A38 at [Esprit School of Engineering](https://esprit.tn). It's a comprehensive agricultural management system that integrates cultivation tracking, weather monitoring, resource management, and e-commerce capabilities for agricultural products.

## Repository Information 
- **GitHub Topics**: symfony, php, agriculture, deep-learning, face-recognition, e-commerce, flask, python, weather-api
- **Description**: Comprehensive agricultural management system with integrated face recognition, deep learning models, and e-commerce capabilities

## Features
- **User Management**: Authentication, registration, and profile management
- **Agricultural Cultivation Tracking**: Monitor and manage agricultural parcels and crops
- **Weather Integration**: Real-time weather data to support farming decisions
- **E-Commerce Platform**: Buy and sell agricultural products
- **Workshop Management**: Schedule and manage agricultural workshops
- **Stock Management**: Track inventory of agricultural products
- **Chatbot Assistant**: AI-powered chat support for agricultural queries
- **Analytics Dashboard**: Visualize agricultural data and statistics
- **Waste Management**: Track and manage agricultural waste
- **Financial Transaction Management**: Track, process, and analyze financial transactions
- **Employee Management**: Handle employee records, scheduling, and payroll management
- **Face ID Authentication**: Biometric authentication using facial recognition
- **Deep Learning Models**: Advanced predictive models for agricultural applications

### Deep Learning Models
- **Freshness Detector**: Determines if fruits and vegetables are fresh or rotten
- **Fruits/Vegetables Identification**: Recognizes types of fruits/vegetables and estimates calorie content
- **Plant Disease Detector**: Identifies plant diseases from images
- **Sales Prediction**: Forecasts product sales volumes based on historical data

## Tech Stack
### Backend
- Symfony 6.4
- PHP 8.1+
- Doctrine ORM
- MySQL Database
- Neo4j (Graph Database)
- Flask (Python microservices)

### Frontend
- Twig Templates
- Webpack Encore
- Stimulus
- Bootstrap

### Other Tools
- OpenWeatherMap API for weather data
- Stripe for payment processing
- Twilio for SMS notifications
- QR Code generation for tracking
- Google OAuth integration
- dlib and OpenCV for facial recognition
- TensorFlow and PyTorch for deep learning models

## Directory Structure
- `src/Controller/`: Application controllers
- `src/Entity/`: Database entity classes
- `src/Repository/`: Database repositories
- `src/Form/`: Form types
- `src/Service/`: Business logic services
- `templates/`: Twig templates
- `public/`: Publicly accessible files
- `assets/`: Source files for CSS, JavaScript, and images
- `python/`: Face ID and other biometric authentication services
- `flask/`: Deep learning models exposed through API endpoints
  - `/model1`: Freshness detection API
  - `/model2`: Food identification and calorie estimation API
  - `/model3`: Plant disease detection API
  - `/predict`: General prediction endpoint for sales forecasting

## Getting Started
1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Configure your environment variables in `.env`
4. Set up your database:
   ```bash
   php bin/console doctrine:database:create
   php bin/console make:migration:migrate
   php bin/console doctrine:migrations:migrate
   ```
5. Build assets:
   ```bash
   npm run build
   ```
6. Start the Symfony development server:
   ```bash
   symfony server:start
   ```

## Running the Deep Learning Models
To run the deep learning models through the Flask API:

1. Set up a Python environment (Python 3.8+ recommended):
   ```bash
   cd flask
   pip install -r requirements.txt
   ```

2. Start the Flask API server:
   ```bash
   python main.py
   ```
   This will start the Flask server on port 5000 with the following endpoints:
   - `http://localhost:5000/model1` - Freshness detection API
   - `http://localhost:5000/model2` - Fruits/Vegetables identification and calorie estimation
   - `http://localhost:5000/model3` - Plant disease detection
   - `http://localhost:5000/predict` - Sales prediction endpoint

3. For the Face ID service:
   ```bash
   cd python
   pip install -r requirements.txt
   python face_recognition_service.py
   ```

## API Usage Examples
- Freshness Detection:
  ```
  POST /model1/predict
  Content-Type: multipart/form-data
  
  file: [image file]
  ```

- Fruits/Vegetables Identification:
  ```
  POST /model2/identify
  Content-Type: multipart/form-data
  
  file: [image file]
  ```

- Plant Disease Detection:
  ```
  POST /model3/diagnose
  Content-Type: multipart/form-data
  
  file: [image file]
  ```

- Sales Prediction:
  ```
  POST /predict
  Content-Type: application/json
  
  {
    "product_id": "123",
    "timeframe": "monthly"
  }
  ```

## Acknowledgments
This project was completed under the guidance of professors at Esprit School of Engineering as part of the 3A38 curriculum for the academic year 2024-2025. 