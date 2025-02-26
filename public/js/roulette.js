// Fichier séparé: public/js/roulette.js
// Version alternative que vous pourriez utiliser à la place du JavaScript intégré dans le template

class PromotionRoulette {
    constructor(options) {
        this.wheelElement = document.querySelector(options.wheelSelector);
        this.buttonElement = document.querySelector(options.buttonSelector);
        this.resultElement = document.querySelector(options.resultSelector);
        this.priceElement = document.querySelector(options.priceSelector);
        this.priceInputElement = document.querySelector(options.priceInputSelector);
        this.postUrl = options.postUrl;
        this.segmentsCount = options.segmentsCount;
        
        this.isSpinning = false;
        this.initEvents();
    }
    
    initEvents() {
        this.buttonElement.addEventListener('click', () => this.spin());
    }
    
    spin() {
        if (this.isSpinning) return;
        
        this.isSpinning = true;
        this.buttonElement.disabled = true;
        this.resultElement.style.display = 'none';
        
        const currentPrice = parseFloat(this.priceElement.textContent);
        
        // Envoyer la requête au serveur
        fetch(this.postUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'prix=' + currentPrice
        })
        .then(response => response.json())
        .then(data => this.handleSpinResult(data))
        .catch(error => {
            console.error('Erreur:', error);
            this.resetSpinState();
        });
    }
    
    handleSpinResult(data) {
        if (!data.success) {
            this.resetSpinState();
            return;
        }
        
        // Calculer l'angle de rotation
        const segmentAngle = 360 / this.segmentsCount;
        const extraAngle = Math.random() * (segmentAngle * 0.8);
        // L'index à l'envers car la roue tourne dans le sens horaire
        const segmentIndex = this.segmentsCount - 1 - data.indexPromotion;
        // Rotations complètes (5) + position finale
        const finalAngle = 5 * 360 + (segmentIndex * segmentAngle) + extraAngle;
        
        // Animation de la roue
        this.wheelElement.style.transition = 'transform 5s cubic-bezier(0.17, 0.67, 0.16, 0.99)';
        this.wheelElement.style.transform = `rotate(${finalAngle}deg)`;
        
        // Afficher le résultat après la fin de l'animation
        setTimeout(() => {
            // Mettre à jour le prix affiché
            this.priceElement.textContent = data.nouveauPrix.toFixed(2);
            this.priceInputElement.value = data.nouveauPrix.toFixed(2);
            
            // Afficher le message de résultat
            this.resultElement.textContent = data.message;
            this.resultElement.style.display = 'block';
            
            this.resetSpinState();
        }, 5000); // 5 secondes = durée de l'animation
    }
    
    resetSpinState() {
        this.isSpinning = false;
        this.buttonElement.disabled = false;
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    new PromotionRoulette({
        wheelSelector: '.wheel',
        buttonSelector: '#spin-button',
        resultSelector: '#result-message',
        priceSelector: '#prix-actuel',
        priceInputSelector: '#prix-input',
        postUrl: '/roulette/tourner', // À ajuster selon votre configuration
        segmentsCount: 8 // Nombre de segments dans la roue
    });
});