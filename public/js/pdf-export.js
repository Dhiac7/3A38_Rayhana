/**
 * Fonction pour exporter les données en PDF
 * Utilise la bibliothèque html2pdf.js avec un style élégant
 */
function exportToPDF(elementId, filename) {
    // Vérifier si html2pdf est disponible
    if (typeof html2pdf === 'undefined') {
        console.error('La bibliothèque html2pdf n\'est pas chargée correctement');
        alert('Erreur: La bibliothèque PDF n\'est pas disponible. Veuillez réessayer plus tard.');
        return;
    }
    
    // Afficher un message de chargement
    const loadingMessage = document.createElement('div');
    loadingMessage.className = 'alert alert-info text-center';
    loadingMessage.style.position = 'fixed';
    loadingMessage.style.top = '50%';
    loadingMessage.style.left = '50%';
    loadingMessage.style.transform = 'translate(-50%, -50%)';
    loadingMessage.style.zIndex = '9999';
    loadingMessage.style.padding = '20px';
    loadingMessage.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Génération du PDF en cours...';
    document.body.appendChild(loadingMessage);

    // Récupérer l'élément à exporter
    const element = document.getElementById(elementId);
    if (!element) {
        console.error(`Élément avec l'ID ${elementId} non trouvé`);
        loadingMessage.remove();
        alert(`Erreur: L'élément avec l'ID ${elementId} n'a pas été trouvé.`);
        return;
    }

    console.log(`Exportation de l'élément avec l'ID ${elementId} en PDF`);
    
    // Déterminer le type de contenu (déchets ou ateliers)
    const isDechet = elementId.includes('dechet');
    const title = isDechet ? 'Liste des Déchets' : 'Liste des Ateliers';
    
    // Créer un nouveau document avec un style élégant
    const pdfContainer = document.createElement('div');
    pdfContainer.style.width = '100%';
    pdfContainer.style.padding = '20px';
    pdfContainer.style.backgroundColor = '#f8f9fa';
    pdfContainer.style.fontFamily = 'Arial, sans-serif';
    
    // Ajouter une feuille de style
    const styleLink = document.createElement('link');
    styleLink.rel = 'stylesheet';
    styleLink.href = '/css/pdf-styles.css';
    pdfContainer.appendChild(styleLink);
    
    // Créer l'en-tête
    const header = document.createElement('div');
    header.className = 'header';
    header.innerHTML = `<h1>${title}</h1>`;
    pdfContainer.appendChild(header);
    
    // Créer le conteneur pour les données
    const cardsContainer = document.createElement('div');
    cardsContainer.className = 'cards-container';
    
    // Traitement différent selon qu'il s'agit de déchets (tableau) ou d'ateliers (cartes)
    if (isDechet) {
        // Pour les déchets, extraire les données du tableau
        const table = element.querySelector('table');
        if (!table) {
            console.error('Tableau non trouvé dans l\'\u00e9lément');
            loadingMessage.remove();
            alert('Erreur: Impossible de trouver les données à exporter.');
            return;
        }
        
        // Obtenir les en-têtes et les données
        const headers = [];
        const rows = [];
        
        // Extraire les en-têtes
        const headerRow = table.querySelector('thead tr');
        if (headerRow) {
            headerRow.querySelectorAll('th').forEach(th => {
                headers.push(th.textContent.trim());
            });
        }
        
        // Extraire les données des lignes
        table.querySelectorAll('tbody tr').forEach(tr => {
            const rowData = [];
            tr.querySelectorAll('td').forEach(td => {
                // Conserver le HTML pour les boutons et autres éléments
                rowData.push({
                    text: td.textContent.trim(),
                    html: td.innerHTML
                });
            });
            rows.push(rowData);
        });
        
        // Créer des cartes pour chaque ligne de données
        rows.forEach((row, index) => {
            const card = document.createElement('div');
            card.className = 'card';
            
            // En-tête de la carte avec le titre principal (première colonne)
            const cardHeader = document.createElement('div');
            cardHeader.className = 'card-header';
            cardHeader.textContent = row[0].text || `Item ${index + 1}`;
            card.appendChild(cardHeader);
            
            // Corps de la carte
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body';
            
            // Ajouter chaque champ comme une ligne dans la carte
            for (let i = 1; i < row.length - 1; i++) { // Ignorer la dernière colonne (actions)
                if (headers[i] && row[i]) {
                    const fieldContainer = document.createElement('div');
                    fieldContainer.style.marginBottom = '10px';
                    
                    const fieldLabel = document.createElement('strong');
                    fieldLabel.textContent = headers[i] + ': ';
                    fieldLabel.style.color = '#2e7d32';
                    
                    const fieldValue = document.createElement('span');
                    fieldValue.textContent = row[i].text;
                    
                    fieldContainer.appendChild(fieldLabel);
                    fieldContainer.appendChild(fieldValue);
                    cardBody.appendChild(fieldContainer);
                }
            }
            
            card.appendChild(cardBody);
            cardsContainer.appendChild(card);
        });
    } else {
        // Pour les ateliers, extraire les données des cartes existantes
        const atelierCards = element.querySelectorAll('.atelier-card');
        if (atelierCards.length === 0) {
            console.error('Aucune carte d\'atelier trouvée dans l\'\u00e9lément');
            loadingMessage.remove();
            alert('Erreur: Impossible de trouver les données à exporter.');
            return;
        }
        
        // Créer une carte pour chaque atelier
        atelierCards.forEach((atelierCard, index) => {
            const card = document.createElement('div');
            card.className = 'card';
            
            // Extraire le titre (nom de l'atelier)
            const titleElement = atelierCard.querySelector('.card-title');
            const title = titleElement ? titleElement.textContent.trim() : `Atelier ${index + 1}`;
            
            // En-tête de la carte
            const cardHeader = document.createElement('div');
            cardHeader.className = 'card-header';
            cardHeader.textContent = title;
            card.appendChild(cardHeader);
            
            // Corps de la carte
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body';
            
            // Extraire les informations de l'atelier
            const cardTexts = atelierCard.querySelectorAll('.card-text');
            cardTexts.forEach(text => {
                if (text.textContent.trim() !== '') {
                    const fieldContainer = document.createElement('div');
                    fieldContainer.style.marginBottom = '10px';
                    fieldContainer.innerHTML = text.innerHTML;
                    cardBody.appendChild(fieldContainer);
                }
            });
            
            card.appendChild(cardBody);
            cardsContainer.appendChild(card);
        });
    }
    
    pdfContainer.appendChild(cardsContainer);
    
    // Ajouter un pied de page
    const footer = document.createElement('div');
    footer.className = 'footer';
    const today = new Date();
    const dateStr = today.toLocaleDateString();
    footer.innerHTML = `<p>Document généré le ${dateStr}</p>`;
    pdfContainer.appendChild(footer);
    
    // Créer un conteneur temporaire hors écran
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.appendChild(pdfContainer);
    document.body.appendChild(tempContainer);
    
    // Options pour html2pdf
    const options = {
        margin: [10, 10, 20, 10], // [top, left, bottom, right]
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    // Générer le PDF
    html2pdf()
        .set(options)
        .from(pdfContainer)
        .save()
        .then(() => {
            // Nettoyage
            document.body.removeChild(tempContainer);
            loadingMessage.remove();
            console.log('PDF généré avec succès');
        })
        .catch(error => {
            console.error('Erreur lors de la génération du PDF:', error);
            document.body.removeChild(tempContainer);
            loadingMessage.remove();
            
            // Afficher un message d'erreur
            const errorMessage = document.createElement('div');
            errorMessage.className = 'alert alert-danger text-center';
            errorMessage.style.position = 'fixed';
            errorMessage.style.top = '50%';
            errorMessage.style.left = '50%';
            errorMessage.style.transform = 'translate(-50%, -50%)';
            errorMessage.style.zIndex = '9999';
            errorMessage.style.padding = '20px';
            errorMessage.textContent = `Erreur lors de la génération du PDF: ${error.message}`;
            document.body.appendChild(errorMessage);
            
            // Supprimer le message d'erreur après 5 secondes
            setTimeout(() => {
                errorMessage.remove();
            }, 5000);
        });
}

// Fonction pour récupérer toutes les données (sans pagination) pour l'export PDF
async function fetchAllDataForExport(type) {
    try {
        // Afficher un message de chargement
        const loadingMessage = document.createElement('div');
        loadingMessage.className = 'alert alert-info text-center';
        loadingMessage.style.position = 'fixed';
        loadingMessage.style.top = '50%';
        loadingMessage.style.left = '50%';
        loadingMessage.style.transform = 'translate(-50%, -50%)';
        loadingMessage.style.zIndex = '9999';
        loadingMessage.style.padding = '20px';
        loadingMessage.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Récupération des données pour l\'export...';
        document.body.appendChild(loadingMessage);
        
        let url;
        if (type === 'dechets') {
            // Utiliser le fallback à l'ancienne méthode pour le moment
            return null;
        } else if (type === 'ateliers') {
            // Utiliser le fallback à l'ancienne méthode pour le moment
            return null;
        } else {
            throw new Error('Type de données non reconnu');
        }
        
        // Récupérer toutes les données
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest', // Indiquer que c'est une requête AJAX
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        loadingMessage.remove();
        return data;
    } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
        alert(`Erreur lors de la récupération des données: ${error.message}`);
        return null;
    }
}

// Fonction pour générer un PDF à partir des données brutes
async function generatePDFFromData(data, type, filename) {
    // Afficher un message de chargement
    const loadingMessage = document.createElement('div');
    loadingMessage.className = 'alert alert-info text-center';
    loadingMessage.style.position = 'fixed';
    loadingMessage.style.top = '50%';
    loadingMessage.style.left = '50%';
    loadingMessage.style.transform = 'translate(-50%, -50%)';
    loadingMessage.style.zIndex = '9999';
    loadingMessage.style.padding = '20px';
    loadingMessage.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Génération du PDF en cours...';
    document.body.appendChild(loadingMessage);
    
    try {
        // Créer un nouveau document avec un style élégant
        const pdfContainer = document.createElement('div');
        pdfContainer.style.width = '100%';
        pdfContainer.style.padding = '20px';
        pdfContainer.style.backgroundColor = '#f8f9fa';
        pdfContainer.style.fontFamily = 'Arial, sans-serif';
        
        // Ajouter une feuille de style
        const styleLink = document.createElement('link');
        styleLink.rel = 'stylesheet';
        styleLink.href = '/css/pdf-styles.css';
        pdfContainer.appendChild(styleLink);
        
        // Créer l'en-tête
        const header = document.createElement('div');
        header.className = 'header';
        const title = type === 'dechets' ? 'Liste des Déchets' : 'Liste des Ateliers';
        header.innerHTML = `<h1>${title}</h1>`;
        pdfContainer.appendChild(header);
        
        // Créer des cartes pour chaque élément
        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'cards-container';
        
        if (type === 'dechets') {
            // Traitement des déchets
            data.forEach((dechet, index) => {
                const card = document.createElement('div');
                card.className = 'card';
                
                // En-tête de la carte
                const cardHeader = document.createElement('div');
                cardHeader.className = 'card-header';
                cardHeader.textContent = dechet.type || `Déchet ${index + 1}`;
                card.appendChild(cardHeader);
                
                // Corps de la carte
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body';
                
                // Ajouter les champs
                const fields = [
                    { label: 'Quantité', value: dechet.quantite },
                    { label: 'Date de production', value: new Date(dechet.dateProduction).toLocaleDateString() },
                    { label: 'Statut', value: dechet.statut },
                    { label: 'Date d\'expiration', value: dechet.dateExpiration ? new Date(dechet.dateExpiration).toLocaleDateString() : 'Non définie' }
                ];
                
                fields.forEach(field => {
                    const fieldContainer = document.createElement('div');
                    fieldContainer.style.marginBottom = '10px';
                    
                    const fieldLabel = document.createElement('strong');
                    fieldLabel.textContent = field.label + ': ';
                    fieldLabel.style.color = '#2e7d32';
                    
                    const fieldValue = document.createElement('span');
                    fieldValue.textContent = field.value;
                    
                    fieldContainer.appendChild(fieldLabel);
                    fieldContainer.appendChild(fieldValue);
                    cardBody.appendChild(fieldContainer);
                });
                
                card.appendChild(cardBody);
                cardsContainer.appendChild(card);
            });
        } else if (type === 'ateliers') {
            // Traitement des ateliers
            data.forEach((atelier, index) => {
                const card = document.createElement('div');
                card.className = 'card';
                
                // En-tête de la carte
                const cardHeader = document.createElement('div');
                cardHeader.className = 'card-header';
                cardHeader.textContent = atelier.nom || `Atelier ${index + 1}`;
                card.appendChild(cardHeader);
                
                // Corps de la carte
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body';
                
                // Ajouter les champs (ajustez selon les propriétés de vos ateliers)
                const fields = [
                    { label: 'Description', value: atelier.description },
                    { label: 'Date', value: atelier.date ? new Date(atelier.date).toLocaleDateString() : 'Non définie' },
                    { label: 'Lieu', value: atelier.lieu || 'Non spécifié' },
                    { label: 'Capacité', value: atelier.capacite || 'Non spécifiée' }
                ];
                
                fields.forEach(field => {
                    const fieldContainer = document.createElement('div');
                    fieldContainer.style.marginBottom = '10px';
                    
                    const fieldLabel = document.createElement('strong');
                    fieldLabel.textContent = field.label + ': ';
                    fieldLabel.style.color = '#2e7d32';
                    
                    const fieldValue = document.createElement('span');
                    fieldValue.textContent = field.value;
                    
                    fieldContainer.appendChild(fieldLabel);
                    fieldContainer.appendChild(fieldValue);
                    cardBody.appendChild(fieldContainer);
                });
                
                card.appendChild(cardBody);
                cardsContainer.appendChild(card);
            });
        }
        
        pdfContainer.appendChild(cardsContainer);
        
        // Ajouter un pied de page
        const footer = document.createElement('div');
        footer.className = 'footer';
        const today = new Date();
        const dateStr = today.toLocaleDateString();
        footer.innerHTML = `<p>Document généré le ${dateStr}</p>`;
        pdfContainer.appendChild(footer);
        
        // Créer un conteneur temporaire hors écran
        const tempContainer = document.createElement('div');
        tempContainer.style.position = 'absolute';
        tempContainer.style.left = '-9999px';
        tempContainer.appendChild(pdfContainer);
        document.body.appendChild(tempContainer);
        
        // Options pour html2pdf
        const options = {
            margin: [10, 10, 20, 10], // [top, left, bottom, right]
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Générer le PDF
        await html2pdf()
            .set(options)
            .from(pdfContainer)
            .save();
        
        // Nettoyage
        document.body.removeChild(tempContainer);
        loadingMessage.remove();
        console.log('PDF généré avec succès');
    } catch (error) {
        console.error('Erreur lors de la génération du PDF:', error);
        loadingMessage.remove();
        
        // Afficher un message d'erreur
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-danger text-center';
        errorMessage.style.position = 'fixed';
        errorMessage.style.top = '50%';
        errorMessage.style.left = '50%';
        errorMessage.style.transform = 'translate(-50%, -50%)';
        errorMessage.style.zIndex = '9999';
        errorMessage.style.padding = '20px';
        errorMessage.textContent = `Erreur lors de la génération du PDF: ${error.message}`;
        document.body.appendChild(errorMessage);
        
        // Supprimer le message d'erreur après 5 secondes
        setTimeout(() => {
            errorMessage.remove();
        }, 5000);
    }
}

// Fonction pour initialiser les boutons d'exportation PDF
function initPdfExportButtons() {
    console.log('Initialisation des boutons d\'exportation PDF');
    
    // Bouton d'exportation des déchets
    const exportDechetsBtn = document.getElementById('export-dechets-pdf');
    if (exportDechetsBtn) {
        exportDechetsBtn.addEventListener('click', function() {
            // Utiliser directement l'ancienne méthode qui fonctionne
            exportToPDF('dechets-table-container', 'liste-dechets.pdf');
        });
        console.log('Bouton d\'exportation des déchets initialisé');
    } else {
        console.log('Bouton d\'exportation des déchets non trouvé');
    }
    
    // Bouton d'exportation des ateliers
    const exportAteliersBtn = document.getElementById('export-ateliers-pdf');
    if (exportAteliersBtn) {
        exportAteliersBtn.addEventListener('click', function() {
            // Utiliser directement l'ancienne méthode qui fonctionne
            exportToPDF('ateliers-table-container', 'liste-ateliers.pdf');
        });
        console.log('Bouton d\'exportation des ateliers initialisé');
    } else {
        console.log('Bouton d\'exportation des ateliers non trouvé');
    }
}

// Initialiser les boutons lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation des boutons d\'exportation PDF');
    initPdfExportButtons();
});
