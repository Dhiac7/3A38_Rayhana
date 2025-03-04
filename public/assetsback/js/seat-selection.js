function selectSeat(element) {
    console.log("Button clicked:", element);  // Vérifie si le bouton est bien cliqué

    // Si la place n'est pas déjà réservée
    if (!element.classList.contains('disabled')) {
        element.classList.toggle('selected');  // Bascule l'état de sélection

        const placeCode = element.dataset.code;  // Récupère le code de la place
        console.log("Place code:", placeCode);  // Affiche le code de la place dans la console

        // Envoie une requête AJAX pour réserver la place
        fetch('/reserve-place', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',  // Envoie du JSON
                'Accept': 'application/json'  // Accepte une réponse JSON
            },
            body: JSON.stringify({ code: placeCode })  // Envoie le code de la place dans le corps
        })
        .then(response => {
            console.log("Response received:", response);  // Affiche la réponse brute
            return response.json();  // Attends la réponse JSON du serveur
        })
        .then(data => {
            console.log("Data received:", data);  // Affiche les données reçues
            if (data.success) {
                alert('Place réservée !');
                element.classList.add('disabled'); // Désactive le bouton après la réservation
                element.disabled = true;  // Désactive également le bouton dans le DOM
            } else {
                alert(data.message || 'Erreur lors de la réservation.');
            }
        })
        .catch(error => {
            console.error("Erreur réseau:", error);
            alert('Erreur de réseau, veuillez réessayer.');
        });
    }
}