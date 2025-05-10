# Agri-Connect Platform

## Overview
This project was developed as part of the coursework for 3A38 at [Esprit School of Engineering](https://esprit.tn). It's a comprehensive agricultural management system that integrates cultivation tracking, weather monitoring, resource management, and e-commerce capabilities for agricultural products.

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

## Tech Stack
### Backend
- Symfony 6.4
- PHP 8.1+
- Doctrine ORM
- MySQL Database
- Neo4j (Graph Database)

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

## Directory Structure
- `src/Controller/`: Application controllers
- `src/Entity/`: Database entity classes
- `src/Repository/`: Database repositories
- `src/Form/`: Form types
- `src/Service/`: Business logic services
- `templates/`: Twig templates
- `public/`: Publicly accessible files
- `assets/`: Source files for CSS, JavaScript, and images

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

## Acknowledgments
This project was completed under the guidance of professors at Esprit School of Engineering as part of the 3A38 curriculum for the academic year 2024-2025. 