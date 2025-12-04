/**
 * MUSEE DETAIL PAGE - JavaScript avec intégration APIs
 */

// Récupérer les données du musée depuis PHP (injectées dans le HTML)
const museumData = MUSEUM_DATA;
let map = null;

console.log('🏛️ Musée chargé:', museumData);

/**
 * Initialiser la page
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Page chargée, début initialisation...');
    
    // Initialiser AOS animations
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true });
    }
    
    // Initialiser la carte
    initMap();
    
    // Charger la météo
    loadWeather();
    
    // Charger les œuvres
    console.log('📦 Appel loadArtworks()...');
    loadArtworks();
});

/**
 * Initialiser la carte Leaflet
 */
function initMap() {
    if (!museumData.latitude || !museumData.longitude) {
        console.warn('⚠️ Pas de coordonnées GPS pour ce musée');
        document.getElementById('museumMap').innerHTML = '<div class="alert alert-info">Localisation non disponible</div>';
        return;
    }

    const lat = parseFloat(museumData.latitude);
    const lng = parseFloat(museumData.longitude);

    // Créer la carte centrée sur le musée
    map = L.map('museumMap').setView([lat, lng], 15);

    // Ajouter la couche OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Créer un marqueur doré personnalisé
    const goldIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Ajouter le marqueur du musée
    L.marker([lat, lng], { icon: goldIcon })
        .addTo(map)
        .bindPopup(`
            <div class="museum-popup">
                <h5>${museumData.name}</h5>
                <p><i class="fas fa-map-marker-alt"></i> ${museumData.address || museumData.city}</p>
            </div>
        `)
        .openPopup();

    console.log('🗺️ Carte initialisée:', lat, lng);
}

/**
 * Charger la météo du musée
 */
async function loadWeather() {
    const widget = document.getElementById('weatherWidget');
    
    if (!museumData.city && !museumData.address) {
        if (widget) widget.style.display = 'none';
        return;
    }

    try {
        // Utiliser la ville du musée
        const city = museumData.city || museumData.address || 'Paris';
        const response = await fetch(`api/weather-simple.php?city=${encodeURIComponent(city)}`);
        const data = await response.json();

        if (data.success && data.weather) {
            const weather = data.weather;
            
            // Vérifier que les éléments existent avant de les modifier
            const weatherIcon = document.getElementById('weatherIcon');
            const weatherTemp = document.getElementById('weatherTemp');
            const weatherDesc = document.getElementById('weatherDesc');
            const weatherHumidity = document.getElementById('weatherHumidity');
            const weatherWind = document.getElementById('weatherWind');
            const weatherFeels = document.getElementById('weatherFeels');
            
            if (weatherIcon) weatherIcon.textContent = getWeatherIcon(weather.description);
            if (weatherTemp) weatherTemp.textContent = weather.temperature;
            if (weatherDesc) weatherDesc.textContent = weather.description;
            if (weatherHumidity) weatherHumidity.textContent = weather.humidity + '%';
            if (weatherWind) weatherWind.textContent = weather.wind_speed + ' km/h';
            if (weatherFeels) weatherFeels.textContent = weather.feels_like + '°C';
            
            console.log('🌤️ Météo chargée pour', city);
        } else {
            console.warn('⚠️ Météo non disponible');
            if (widget) widget.style.display = 'none';
        }
    } catch (error) {
        console.error('❌ Erreur météo:', error);
        if (widget) widget.style.display = 'none';
    }
}

/**
 * Charger les œuvres du musée
 */
async function loadArtworks() {
    const container = document.getElementById('artworksContainer');
    
    if (!container) {
        console.error('❌ Container introuvable!');
        return;
    }

    console.log('🎨 Chargement des œuvres...');

    try {
        // Appel API direct
        const response = await fetch(`api/museum-artworks.php?id=${museumData.id}&source=met&limit=9`);
        const data = await response.json();

        if (data.success && data.artworks && data.artworks.length > 0) {
            console.log(`✅ ${data.artworks.length} œuvres reçues`);
            
            // Générer le HTML
            let html = '';
            data.artworks.forEach(artwork => {
                const img = artwork.primaryImageSmall || artwork.primaryImage || 'https://via.placeholder.com/400x300/1a4d7a/ffffff?text=Oeuvre';
                const titre = artwork.title || 'Sans titre';
                const artiste = artwork.artistDisplayName || 'Artiste inconnu';
                const date = artwork.objectDate || '';

                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="artwork-card">
                            <div class="artwork-image">
                                <img src="${img}" alt="${titre}">
                            </div>
                            <div class="artwork-info">
                                <h5 class="artwork-title">${titre}</h5>
                                <p class="artwork-artist"><i class="fas fa-user-circle me-1"></i> ${artiste}</p>
                                ${date ? `<p class="artwork-date"><i class="far fa-calendar me-1"></i> ${date}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            console.log('✅ Œuvres affichées!');
        } else {
            // Pas d'œuvres disponibles
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-palette fa-3x mb-3" style="color: #64748b;"></i>
                    <p style="color: #94a3b8;">Aucune œuvre disponible pour ce musée</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Erreur:', error);
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #f59e0b;"></i>
                <p style="color: #94a3b8;">Erreur lors du chargement des œuvres</p>
            </div>
        `;
    }
}

/**
 * Obtenir l'icône météo
 */
function getWeatherIcon(description) {
    if (!description) return '🌤️';
    
    const desc = description.toLowerCase();
    
    if (desc.includes('clair') || desc.includes('clear') || desc.includes('ensoleillé')) return '☀️';
    if (desc.includes('nuage') || desc.includes('cloud') || desc.includes('couvert')) return '☁️';
    if (desc.includes('pluie') || desc.includes('rain') || desc.includes('pluvieux')) return '🌧️';
    if (desc.includes('neige') || desc.includes('snow')) return '❄️';
    if (desc.includes('orage') || desc.includes('thunder') || desc.includes('storm')) return '⛈️';
    if (desc.includes('bruine') || desc.includes('drizzle')) return '🌦️';
    if (desc.includes('brume') || desc.includes('mist') || desc.includes('fog') || desc.includes('brouillard')) return '🌫️';
    
    return '🌤️';
}

/**
 * Déterminer quelle API utiliser selon le pays du musée
 */
function getApiSource(country) {
    const apiMap = {
        'France': 'met',
        'United Kingdom': 'met',
        'USA': 'met',
        'Japan': 'met',
        'Netherlands': 'rijksmuseum'
    };
    return apiMap[country] || 'met';
}
