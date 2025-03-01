// Fonction pour gérer la sélection d'une place
function selectSeat(element) {
    if (!element.classList.contains('disabled')) {
        element.classList.toggle('selected');  // Bascule l'état de sélection

        const placeCode = element.dataset.code;  // Récupère le code de la place

        // Envoie une requête AJAX pour réserver la place
        fetch('/reserve-place', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',  // Envoie du JSON
            },
            body: JSON.stringify({ code: placeCode })  // Envoie le code de la place dans le corps
        })
        .then(response => response.json())  // Attends la réponse JSON du serveur
        .then(data => {
            if (data.success) {
                alert('Place réservée !');
            } else {
                alert(data.message || 'Erreur lors de la réservation.');
            }
        })
        .catch(error => {
            alert('Erreur de réseau, veuillez réessayer.');
        });
    }
}
