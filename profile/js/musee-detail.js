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
    console.log('Page chargée, début initialisation...');
    
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
        console.warn('Pas de coordonnées GPS pour ce musée');
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
        
        // APPEL DIRECT À L'API OPENWEATHER
        const weather = await getWeather(city);

        if (weather) {
            
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
            console.warn('Météo non disponible');
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

    console.log('Chargement des œuvres...');

    try {
        // APPEL À LA FONCTION COMBINÉE avec le nom du musée
        const artworks = await getCombinedArtworks(museumData.name, 20);

        if (artworks && artworks.length > 0) {
            console.log(`${artworks.length} œuvres reçues`);
            
            // Initialiser la pagination
            window.allArtworks = artworks;
            window.currentPage = 1;
            window.artworksPerPage = 9;
            
            // Afficher la première page
            displayArtworksPage(1);
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
 * Afficher une page d'œuvres avec pagination
 */
function displayArtworksPage(page) {
    // Scroll vers le haut de la section des œuvres
    const container = document.getElementById('artworksContainer');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    const artworks = window.allArtworks || [];
    const perPage = window.artworksPerPage || 9;
    
    const startIndex = (page - 1) * perPage;
    const endIndex = startIndex + perPage;
    const pageArtworks = artworks.slice(startIndex, endIndex);
    
    // Générer le HTML des œuvres
    let html = '';
    pageArtworks.forEach(artwork => {
        const titre = artwork.title || 'Sans titre';
        const artiste = artwork.artist || 'Artiste inconnu';
        const date = artwork.date || '';
        const source = artwork.source || '';
        
        // Afficher l'image ou le placeholder
        let imgHtml;
            if (artwork.hasImage && artwork.image) {
                imgHtml = `<img src="${artwork.image}" alt="${titre}" style="width: 100%; height: 300px; object-fit: cover;" onerror="const p=this.parentElement; p.classList.add('no-image'); p.innerHTML='<div style=\\'height: 300px; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); display: flex; align-items: center; justify-content: center; color: white; text-align: center; padding: 20px;\\'><div><i class=\\'fas fa-image fa-3x mb-2\\'></i><p style=\\'margin: 0; font-size: 16px;\\'>Image non disponible</p></div></div>';">`;
        } else {
            imgHtml = `<div style="height: 300px; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); display: flex; align-items: center; justify-content: center; color: white; text-align: center; padding: 20px;">
                <div>
                    <i class="fas fa-image fa-3x mb-2"></i>
                    <p style="margin: 0; font-size: 16px;">Image non disponible</p>
                </div>
            </div>`;
        }
        
        const artworkData = JSON.stringify({
            title: titre,
            artist: artiste,
            date: date,
            source: source,
            image: artwork.image || '',
            hasImage: artwork.hasImage,
            description: artwork.description || '',
            link: artwork.link || ''
        }).replace(/"/g, '&quot;');

        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="artwork-card" style="cursor: pointer;" onclick='showArtworkDetails(${artworkData})'>
                    <div class="artwork-image${artwork.hasImage && artwork.image ? '' : ' no-image'}">
                        ${imgHtml}
                    </div>
                    <div class="artwork-info">
                        <h5 class="artwork-title">${titre}</h5>
                        <p class="artwork-artist"><i class="fas fa-user-circle me-1"></i> ${artiste}</p>
                        ${date ? `<p class="artwork-date"><i class="far fa-calendar me-1"></i> ${date}</p>` : ''}
                        ${source ? `<p class="artwork-source" style="font-size: 0.8rem; color: #94a3b8;"><i class="fas fa-database me-1"></i> ${source}</p>` : ''}
                        <p style="font-size: 0.85rem; color: #c9a961; margin-top: 10px;"><i class="fas fa-info-circle me-1"></i> Cliquer pour détails</p>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Afficher la pagination
    displayPagination(page, artworks.length, perPage);
    
    console.log(`Page ${page} affichée (${pageArtworks.length} œuvres)`);
}

/**
 * Afficher les boutons de pagination
 */
function displayPagination(currentPage, totalItems, perPage) {
    const totalPages = Math.ceil(totalItems / perPage);
    
    // Trouver ou créer le container de pagination
    let paginationContainer = document.getElementById('artworksPagination');
    if (!paginationContainer) {
        paginationContainer = document.createElement('div');
        paginationContainer.id = 'artworksPagination';
        paginationContainer.style.cssText = 'text-align: center; margin-top: 30px;';
        document.getElementById('artworksContainer').parentElement.appendChild(paginationContainer);
    }
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let html = '<div style="display: inline-flex; gap: 10px; align-items: center;">';
    
    // Bouton précédent
    if (currentPage > 1) {
        html += `<button onclick="displayArtworksPage(${currentPage - 1})" style="background: #c9a961; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">
            <i class="fas fa-chevron-left"></i> Précédent
        </button>`;
    }
    
    // Numéros de pages
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            html += `<button style="background: #1a4d7a; color: white; border: none; padding: 10px 15px; border-radius: 8px; font-weight: bold; font-size: 16px;">${i}</button>`;
        } else {
            html += `<button onclick="displayArtworksPage(${i})" style="background: #e2e8f0; color: #1a4d7a; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 16px;">${i}</button>`;
        }
    }
    
    // Bouton suivant
    if (currentPage < totalPages) {
        html += `<button onclick="displayArtworksPage(${currentPage + 1})" style="background: #c9a961; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">
            Suivant <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    html += '</div>';
    paginationContainer.innerHTML = html;
}

/**
 * Afficher les détails d'une œuvre dans un modal
 */
function showArtworkDetails(artwork) {
    // Créer le modal s'il n'existe pas
    let modal = document.getElementById('artworkModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'artworkModal';
        modal.innerHTML = `
            <div class="modal-backdrop" onclick="closeArtworkModal()"></div>
            <div class="modal-content-artwork">
                <button class="modal-close" onclick="closeArtworkModal()">
                    <i class="fas fa-times"></i>
                </button>
                <div id="modalBody"></div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Ajouter les styles CSS
        const style = document.createElement('style');
        style.textContent = `
            #artworkModal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
            }
            #artworkModal.show {
                display: block;
            }
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.85);
                z-index: 9999;
            }
            .modal-content-artwork {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 16px;
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                z-index: 10000;
            }
            .modal-close {
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 20px;
                color: #1a4d7a;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                transition: all 0.3s;
                z-index: 10;
            }
            .modal-close:hover {
                background: #c9a961;
                color: white;
                transform: rotate(90deg);
            }
        `;
        document.head.appendChild(style);
    }
    
    // Remplir le contenu
    const modalBody = document.getElementById('modalBody');
    const imgHtml = artwork.hasImage && artwork.image
        ? `<img src="${artwork.image}" alt="${artwork.title}" style="width: 100%; max-height: 400px; object-fit: contain; border-radius: 12px 12px 0 0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="display:none; width: 100%; height: 400px; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); align-items: center; justify-content: center; color: white; text-align: center; border-radius: 12px 12px 0 0;">
            <div><i class="fas fa-image fa-3x mb-3"></i><p style="margin: 0; font-size: 18px;">Image non disponible</p></div>
        </div>`
        : `<div style="height: 300px; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); display: flex; align-items: center; justify-content: center; color: white; border-radius: 12px 12px 0 0;">
            <div><i class="fas fa-image fa-4x mb-3"></i><p>Image non disponible</p></div>
        </div>`;
    
    modalBody.innerHTML = `
        ${imgHtml}
        <div style="padding: 30px;">
            <h2 style="color: #1a4d7a; margin-bottom: 20px;">${artwork.title}</h2>
            <div style="display: grid; gap: 15px;">
                <div>
                    <strong style="color: #666;"><i class="fas fa-user-circle me-2"></i>Artiste:</strong>
                    <p style="margin: 5px 0 0 0; font-size: 1.1rem;">${artwork.artist}</p>
                </div>
                ${artwork.date ? `
                    <div>
                        <strong style="color: #666;"><i class="far fa-calendar me-2"></i>Date:</strong>
                        <p style="margin: 5px 0 0 0;">${artwork.date}</p>
                    </div>
                ` : ''}
                ${artwork.description ? `
                    <div>
                        <strong style="color: #666;"><i class="fas fa-info-circle me-2"></i>Description:</strong>
                        <p style="margin: 5px 0 0 0; line-height: 1.6;">${artwork.description}</p>
                    </div>
                ` : ''}
                <div>
                    <strong style="color: #666;"><i class="fas fa-database me-2"></i>Source:</strong>
                    <p style="margin: 5px 0 0 0;">${artwork.source}</p>
                </div>
                ${artwork.link ? `
                    <div>
                        <a href="${artwork.link}" target="_blank" class="btn" style="background: #c9a961; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin-top: 10px;">
                            <i class="fas fa-external-link-alt me-2"></i>Voir plus de détails
                        </a>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    // Afficher le modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Fermer le modal
 */
function closeArtworkModal() {
    const modal = document.getElementById('artworkModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
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
