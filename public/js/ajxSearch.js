document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const parcellesContainer = document.getElementById('parcelles-container');

    // Check if the input field exists
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            let query = this.value;
            console.log('Search query:', query);

            // Make the AJAX request
            fetch("{{ path('app_parcelle_index') }}?q=" + encodeURIComponent(query), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('AJAX Response Data:', data);

                // Clear previous results
                parcellesContainer.innerHTML = '';

                // Handle no results
                if (data.length === 0) {
                    parcellesContainer.innerHTML = '<p class="text-center text-bgray-700 dark:text-bgray-50">Aucune parcelle trouvée.</p>';
                } else {
                    // Loop over the received data and render it
                    data.forEach(parcelle => {
                        const parcelleHTML = `
                            <div class="parcelle-item rounded-lg bg-white p-5 dark:bg-darkblack-600 shadow-lg">
                                <a href="/parcelle/${parcelle.id}">
                                    <div id="map-${parcelle.id}" class="mini-map rounded-lg overflow-hidden" data-lat="${parcelle.latitude}" data-lng="${parcelle.longitude}"></div>
                                </a>
                                <div class="mt-4 flex items-center space-x-3">
                                    <img src="{{ asset('assets/images/icons/farm.svg') }}" alt="icon" class="w-8 h-8">
                                    <p class="text-lg font-semibold text-bgray-900 dark:text-white">
                                        ${parcelle.nom} - Irrigation: ${parcelle.irrigationDisponible ? 'Oui' : 'Non'}
                                    </p>
                                </div>
                                <h4 class="text-bgray-700 dark:text-bgray-50">
                                    ${parcelle.superficie} ha - ${parcelle.latitude}° ${parcelle.longitude}°
                                </h4>
                            </div>
                        `;
                        parcellesContainer.innerHTML += parcelleHTML;
                    });
                }

                // Reload maps for new parcelles (if any)
                //reloadMaps();
            })
            .catch(error => {
                console.error('AJAX error:', error);
            });
        });
    } else {
        console.error('Search input element not found!');
    }
});
/*
// Helper function to reload map instances after results are updated
function reloadMaps() {
    // Make sure to initialize maps for each new parcelle if needed
    {% for parcelle in parcelles %}
        var map{{ parcelle.id }} = new mapboxgl.Map({
            container: 'map-{{ parcelle.id }}',
            style: 'mapbox://styles/mapbox/satellite-streets-v11',
            center: [{{ parcelle.longitude }}, {{ parcelle.latitude }}],
            zoom: 16,
            interactive: false
        });
        new mapboxgl.Marker().setLngLat([{{ parcelle.longitude }}, {{ parcelle.latitude }}]).addTo(map{{ parcelle.id }}); 
    {% endfor %}
}*/
