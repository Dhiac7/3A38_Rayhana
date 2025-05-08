// Fonction de gestion du clic sur le bouton d'empreinte carbone
function handleCarbonButtonClick() {
    console.log('Bouton empreinte carbone cliqué');
    const dechetId = this.getAttribute('data-id');
    const dechetType = this.getAttribute('data-type');
    const dechetQuantite = this.getAttribute('data-quantite');
    const row = this.closest('tr');
    
    // Afficher un indicateur de chargement
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = `loading-${dechetId}`;
    loadingIndicator.innerHTML = '<div class="text-center mt-2"><i class="mdi mdi-loading mdi-spin"></i> Calcul en cours...</div>';
    
    // Vérifier si un résultat existe déjà
    let resultContainer = row.querySelector('.carbon-result');
    if (!resultContainer) {
        resultContainer = document.createElement('td');
        resultContainer.className = 'carbon-result';
        resultContainer.setAttribute('colspan', '6');
        
        // Créer une nouvelle ligne pour le résultat
        const resultRow = document.createElement('tr');
        resultRow.className = `carbon-result-row carbon-result-${dechetId}`;
        resultRow.appendChild(resultContainer);
        
        // Insérer après la ligne actuelle
        row.parentNode.insertBefore(resultRow, row.nextSibling);
    }
    
    resultContainer.innerHTML = '';
    resultContainer.appendChild(loadingIndicator);
    
    // Paramètres pour l'API en fonction du type de déchet
    let apiParams = {};
    
    switch(dechetType) {
        case 'organique':
            apiParams = {
                type: 'waste',
                wasteType: 'organic',
                weight: dechetQuantite
            };
            break;
        case 'plastique':
            apiParams = {
                type: 'waste',
                wasteType: 'plastic',
                weight: dechetQuantite
            };
            break;
        case 'métalique':
            apiParams = {
                type: 'waste',
                wasteType: 'metal',
                weight: dechetQuantite
            };
            break;
        case 'vegetale':
            apiParams = {
                type: 'waste',
                wasteType: 'vegetable',
                weight: dechetQuantite
            };
            break;
        default:
            apiParams = {
                type: 'waste',
                wasteType: 'mixed',
                weight: dechetQuantite
            };
    }
    
    // Simulation de l'API pour test (car l'API réelle nécessite une clé)
    // En production, remplacez cette partie par l'appel API réel
    setTimeout(() => {
        // Simuler une réponse de l'API
        const mockData = {
            carbonFootprint: (Math.random() * 10).toFixed(2),
            environmentalImpact: "Modéré",
            recommendations: "Recycler ce déchet pour réduire l'impact environnemental."
        };
        
        // Supprimer l'indicateur de chargement
        loadingIndicator.remove();
        
        // Afficher les résultats
        const carbonResult = document.createElement('div');
        carbonResult.className = 'card p-3 mt-2 mb-2';
        carbonResult.innerHTML = `
            <h5 class="card-title">Empreinte Carbone - ${dechetType}</h5>
            <div class="card-body">
                <p><strong>Émissions CO2:</strong> ${mockData.carbonFootprint} kg CO2e</p>
                <p><strong>Impact environnemental:</strong> ${mockData.environmentalImpact}</p>
                <p><strong>Recommandations:</strong> ${mockData.recommendations}</p>
            </div>
            <button type="button" class="btn btn-sm close-result" style="background-color: #fff; color: #2F4F4F;">
                <i class="mdi mdi-close"></i> Fermer
            </button>
        `;
        
        resultContainer.appendChild(carbonResult);
        
        // Ajouter un gestionnaire d'événements pour le bouton de fermeture
        carbonResult.querySelector('.close-result').addEventListener('click', function() {
            const resultRow = document.querySelector(`.carbon-result-${dechetId}`);
            if (resultRow) resultRow.remove();
        });
    }, 1500); // Simuler un délai d'API de 1.5 secondes
}

// Fonction pour initialiser les boutons d'empreinte carbone
function initCarbonFootprintButtons() {
    console.log('Initialisation des boutons d\'empreinte carbone');
    const carbonButtons = document.querySelectorAll('.carbon-footprint-btn');
    console.log('Nombre de boutons trouvés:', carbonButtons.length);
    
    carbonButtons.forEach(function (button) {
        // Détacher les anciens gestionnaires d'événements pour éviter les doublons
        button.removeEventListener('click', handleCarbonButtonClick);
        // Ajouter le nouveau gestionnaire d'événements
        button.addEventListener('click', handleCarbonButtonClick);
    });
}

// Fonction globale pour initialiser les boutons après un chargement AJAX
function initCarbonAfterAjax() {
    console.log('Initialisation après AJAX');
    setTimeout(initCarbonFootprintButtons, 100);
}

// Initialiser les boutons si le DOM est déjà chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation des boutons d\'empreinte carbone');
    initCarbonFootprintButtons();
});

// Exposer les fonctions globalement
window.handleCarbonButtonClick = handleCarbonButtonClick;
window.initCarbonFootprintButtons = initCarbonFootprintButtons;
window.initCarbonAfterAjax = initCarbonAfterAjax;
