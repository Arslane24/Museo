/**
 * EXPLORER PAGE - JavaScript moderne avec recherche en base de données
 */

// État de l'application
const state = {
    currentPage: 1,
    currentCategory: 'all',
    currentSearch: '',
    currentCountry: '',
    totalResults: 0,
    loading: false
};

/**
 * Initialisation au chargement de la page
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser AOS animations
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true });
    }
    
    // Charger les musées initiaux
    loadMuseums();
    
    // Écouter les événements
    initEventListeners();
});

/**
 * Initialise tous les event listeners
 */
function initEventListeners() {
    // Bouton de recherche
    document.getElementById('searchBtn')?.addEventListener('click', performSearch);
    
    // Recherche au clavier (Enter)
    document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') performSearch();
    });
    
    // Custom country dropdown
    initCustomCountryDropdown();
    
    // Autocomplétion de recherche
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        console.log('🔍 Autocomplétion activée');
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            console.log('Saisie:', query);
            
            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                console.log('Recherche suggestions pour:', query);
                fetchSearchSuggestions(query);
            }, 300);
        });
    } else {
        console.error('❌ searchInput non trouvé');
    }
    
    // Cacher les suggestions quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-box')) {
            hideSuggestions();
        }
    });
    
    // Filtre de pays
    document.getElementById('countryFilter')?.addEventListener('change', performSearch);
    
    // Bouton reset
    document.getElementById('resetBtn')?.addEventListener('click', resetFilters);
    
    // Filtres de catégorie
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Retirer l'active des autres
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            // Ajouter active à celui-ci
            this.classList.add('active');
            
            // Mettre à jour l'état et rechercher
            state.currentCategory = this.dataset.category;
            state.currentPage = 1;
            loadMuseums();
        });
    });
}

/**
 * Effectue une recherche
 */
async function performSearch() {
    state.currentSearch = document.getElementById('searchInput').value.trim();
    state.currentCountry = document.getElementById('countryFilter').value;
    state.currentPage = 1;
    
    // Charger les musées
    await loadMuseums();
    
    // Si on a un terme de recherche, centrer la carte sur le premier résultat
    if (state.currentSearch && window.museumMap) {
        centerMapOnSearch(state.currentSearch);
    }
}

/**
 * Réinitialise tous les filtres
 */
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('countryFilter').value = '';
    
    // Réinitialiser le dropdown custom du pays
    document.getElementById('countryTrigger').textContent = 'Tous les pays';
    document.querySelectorAll('.country-option').forEach(opt => opt.classList.remove('selected'));
    document.querySelector('.country-option[data-value=""]').classList.add('selected');
    
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.filter-btn[data-category="all"]').classList.add('active');
    
    state.currentSearch = '';
    state.currentCountry = '';
    state.currentCategory = 'all';
    state.currentPage = 1;
    
    loadMuseums();
}

/**
 * Centre la carte sur le musée recherché
 */
async function centerMapOnSearch(searchTerm) {
    if (!window.museumMap || !markersData || markersData.length === 0) {
        console.log('⚠️ Carte ou markers non disponibles');
        return;
    }
    
    try {
        // Chercher dans les données des markers déjà chargés
        const searchLower = searchTerm.toLowerCase();
        const found = markersData.find(data => 
            data.museum.name.toLowerCase().includes(searchLower) ||
            data.museum.city.toLowerCase().includes(searchLower) ||
            data.museum.country.toLowerCase().includes(searchLower)
        );
        
        if (found) {
            const museum = found.museum;
            console.log('🗺️ Centrage carte sur:', museum.name);
            
            // Centrer et zoomer sur le musée
            window.museumMap.setView([museum.latitude, museum.longitude], 15, {
                animate: true,
                duration: 1
            });
            
            // Ouvrir le popup
            found.marker.openPopup();
            
            // Animer le marker (bounce)
            setTimeout(() => {
                const icon = found.marker.getElement();
                if (icon) {
                    icon.classList.add('marker-bounce');
                    setTimeout(() => icon.classList.remove('marker-bounce'), 2000);
                }
            }, 500);
            
            // Scroller vers la carte
            setTimeout(() => {
                document.getElementById('museumMap')?.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 300);
        } else {
            console.log('❌ Aucun musée trouvé pour:', searchTerm);
            // Réinitialiser la vue pour montrer tous les markers
            if (museumMarkers.length > 0) {
                const group = L.featureGroup(museumMarkers);
                window.museumMap.fitBounds(group.getBounds().pad(0.1));
            }
        }
    } catch (error) {
        console.error('Erreur centrage carte:', error);
    }
}

/**
 * Charge les musées depuis l'API
 */
async function loadMuseums() {
    if (state.loading) return;
    
    state.loading = true;
    showLoader();
    
    try {
        const params = new URLSearchParams({
            category: state.currentCategory,
            search: state.currentSearch,
            country: state.currentCountry,
            page: state.currentPage
        });
        
        const response = await fetch(`api/museums-search.php?${params}`);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Afficher la section des résultats
            document.getElementById('resultsSection').style.display = 'block';
            
            // Afficher les musées
            displayMuseums(data.museums);
            updateResultsInfo(data.pagination);
            
            // Gérer la pagination de manière simple
            showPaginationIfNeeded(data.pagination);
            
            state.totalResults = data.pagination.total_results;
        } else {
            showError(data.error || 'Erreur lors du chargement');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    } finally {
        state.loading = false;
    }
}

/**
 * Charge et affiche un musée spécifique par son ID
 */
async function loadSpecificMuseum(museumId) {
    if (state.loading) return;
    
    state.loading = true;
    showLoader();
    
    try {
        const url = `api/museum-detail.php?id=${museumId}`;
        console.log('🎯 Chargement musée spécifique:', museumId);
        
        const response = await fetch(url);
        console.log('📡 Réponse HTTP:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📦 Musée reçu:', data);
        
        if (data.success && data.museum) {
            console.log(`✅ Musée chargé: ${data.museum.name}`);
            // Afficher uniquement ce musée
            displayMuseums([data.museum]);
            // Mettre à jour les infos avec 1 résultat
            updateResultsInfo({
                total_results: 1,
                current_page: 1,
                total_pages: 1,
                results_per_page: 1
            });
            // Pas de pagination pour un seul musée
            const paginationContainer = document.getElementById('paginationContainer');
            if (paginationContainer) {
                paginationContainer.innerHTML = '';
                paginationContainer.style.display = 'none';
            }
        } else {
            console.error('❌ Erreur API:', data.error);
            showError(data.error || 'Musée introuvable');
        }
    } catch (error) {
        console.error('❌ Erreur loadSpecificMuseum:', error);
        showError('Erreur de connexion au serveur: ' + error.message);
    } finally {
        state.loading = false;
    }
}

/**
 * Affiche les musées dans la grille
 */
function displayMuseums(museums) {
    const grid = document.getElementById('museumGrid');
    
    if (museums.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">Aucun musée trouvé</h3>
                <p class="text-muted">Essayez de modifier vos critères de recherche</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = museums.map(museum => createMuseumCard(museum)).join('');
    
    // Ajouter les événements aux cartes (avec un petit délai pour être sûr que le DOM est prêt)
    setTimeout(() => {
        attachCardEvents();
    }, 100);
}

/**
 * Crée une carte de musée
 */
function createMuseumCard(museum) {
    const isFavorite = museum.is_favorite ? 'active' : '';
    const priceValue = parseFloat(museum.price_adult || 0);
    const price = priceValue === 0 ? 'Gratuit' : `${priceValue.toFixed(2)}€`;
    const imageUrl = museum.image_url || museum.cover_image || 'https://images.unsplash.com/photo-1564399579883-451a5d44ec08?w=800';
    
    // Vérifier que le slug existe
    if (!museum.slug) {
        console.warn('⚠️ Musée sans slug:', museum.name, museum);
    }
    
    return `
        <div class="museum-card" data-aos="fade-up" data-museum-id="${museum.id}">
            <div class="museum-image">
                <img src="${imageUrl}" alt="${museum.name}" 
                     onerror="this.src='https://images.unsplash.com/photo-1566305977571-5666677c6e98?w=800'">
                <button class="favorite-btn ${isFavorite}" 
                        data-museum-id="${museum.id}" 
                        onclick="toggleFavorite(${museum.id}, this)">
                    <i class="fas fa-heart"></i>
                </button>
                <span class="museum-badge">${price}</span>
            </div>
            <div class="museum-content">
                <h3 class="museum-title">${museum.name}</h3>
                <div class="museum-location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${museum.city}, ${museum.country}
                </div>
                <p class="museum-description">${museum.short_description || museum.description}</p>
                <div class="museum-stats">
                    <div class="stat-item">
                        <i class="fas fa-palette"></i>
                        <span>${formatNumber(museum.total_artworks)} œuvres</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-star"></i>
                        <span>${museum.rating}/5</span>
                    </div>
                </div>
                <div class="museum-footer">
                    <button class="btn-view" data-museum-slug="${museum.slug}">
                        <i class="fas fa-eye me-2"></i>Découvrir
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Attacher les événements aux cartes de musées
 */
function attachCardEvents() {
    // Événements pour les boutons "Découvrir"
    const buttons = document.querySelectorAll('.btn-view');
    console.log(`🔘 Attachement de ${buttons.length} boutons "Découvrir"`);
    
    if (buttons.length === 0) {
        console.warn('⚠️ Aucun bouton .btn-view trouvé dans le DOM');
        console.log('Grid HTML:', document.getElementById('museumGrid')?.innerHTML?.substring(0, 500));
    }
    
    buttons.forEach((button, index) => {
        const slug = button.dataset.museumSlug;
        console.log(`  Bouton ${index + 1}: slug = "${slug}"`);
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎯 Clic sur bouton, slug:', slug);
            viewMuseum(slug);
        });
    });
}

/**
 * Toggle favori
 */
async function toggleFavorite(museumId, button) {
    // Vérifier si l'utilisateur est connecté
    // TODO: Implémenter avec session PHP
    
    button.classList.toggle('active');
    
    // Animation
    button.style.transform = 'scale(1.3)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 200);
}

/**
 * Voir un musée
 */
function viewMuseum(slug) {
    console.log('🔍 Navigation vers musée:', slug);
    if (!slug || slug === 'undefined' || slug === 'null') {
        console.error('❌ Slug invalide:', slug);
        alert('Erreur: impossible de charger ce musée (slug invalide)');
        return;
    }
    console.log('✅ Redirection vers:', `musee-detail.php?slug=${slug}`);
    window.location.href = `musee-detail.php?slug=${slug}`;
}

// Rendre la fonction accessible globalement
window.viewMuseum = viewMuseum;

/**
 * Affiche la pagination seulement si nécessaire
 */
function showPaginationIfNeeded(pagination) {
    const container = document.getElementById('paginationContainer');
    if (!container) return;
    
    const total = pagination.total_results || 0;
    const perPage = pagination.per_page || 12;
    const totalPages = pagination.total_pages || 0;
    const currentPage = pagination.current_page || 1;
    
    // Si moins de résultats que la limite par page, ne rien afficher
    if (total <= perPage || totalPages <= 1) {
        container.innerHTML = '';
        container.style.display = 'none';
        return;
    }
    
    // Afficher la pagination
    container.style.display = 'flex';
    
    let html = '';
    
    // Bouton précédent
    html += `
        <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} 
                onclick="goToPage(${currentPage - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>
    `;
    
    // Numéros de pages (max 5 pages visibles)
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="page-btn ${i === currentPage ? 'active' : ''}" 
                    onclick="goToPage(${i})">
                ${i}
            </button>
        `;
    }
    
    // Bouton suivant
    html += `
        <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} 
                onclick="goToPage(${currentPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    
    container.innerHTML = html;
}

/**
 * Aller à une page
 */
function goToPage(page) {
    state.currentPage = page;
    loadMuseums();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Met à jour les infos de résultats
 */
function updateResultsInfo(pagination) {
    const titleEl = document.getElementById('resultsTitle');
    const countEl = document.getElementById('resultsCount');
    
    if (!titleEl || !countEl) {
        console.warn('⚠️ Éléments resultsTitle ou resultsCount non trouvés');
        return;
    }
    
    let title = 'Tous les musées';
    if (state.currentSearch) {
        title = `Résultats pour "${state.currentSearch}"`;
    } else if (state.currentCategory !== 'all') {
        title = `Musées - ${getCategoryName(state.currentCategory)}`;
    }
    
    titleEl.textContent = title;
    countEl.textContent = `${pagination.total_results} musée(s) trouvé(s)`;
}

/**
 * Obtient le nom de la catégorie
 */
function getCategoryName(category) {
    const names = {
        'all': 'Tous',
        'art': 'Art Classique',
        'modern': 'Art Moderne',
        'contemporary': 'Contemporain',
        'history': 'Histoire',
        'science': 'Science'
    };
    return names[category] || category;
}

/**
 * Formate un nombre
 */
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(0) + 'K';
    }
    return num;
}

/**
 * Affiche le loader
 */
function showLoader() {
    const grid = document.getElementById('museumGrid');
    grid.innerHTML = `
        <div class="skeleton-loader">
            ${Array(6).fill().map(() => `
                <div class="skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line w-75"></div>
                        <div class="skeleton-line w-50"></div>
                        <div class="skeleton-line w-100"></div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

/**
 * Affiche une erreur
 */
function showError(message) {
    const grid = document.getElementById('museumGrid');
    grid.innerHTML = `
        <div class="col-12 text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h3 class="text-danger">Erreur</h3>
            <p class="text-muted">${message}</p>
            <button class="btn btn-gradient mt-3" onclick="loadMuseums()">
                <i class="fas fa-redo me-2"></i>Réessayer
            </button>
        </div>
    `;
}

// ===========================================
// NOUVELLES FONCTIONNALITÉS APIs
// ===========================================

/**
 * Charge les œuvres du MET Museum
 */
async function loadMetArtworks() {
    const grid = document.getElementById('metArtworksGrid');
    
    try {
        const response = await fetch('api/museum-artworks.php?id=11&source=met&limit=6');
        const data = await response.json();
        
        if (data.success && data.artworks && data.artworks.length > 0) {
            grid.innerHTML = data.artworks.map(artwork => `
                <div class="col-md-6 col-lg-4">
                    <div class="artwork-card">
                        <img src="${artwork.primaryImage || 'https://via.placeholder.com/400x300?text=Oeuvre'}" 
                             alt="${artwork.title || 'Sans titre'}" 
                             class="artwork-card-img"
                             onerror="this.src='https://via.placeholder.com/400x300?text=Image+indisponible'">
                        <div class="artwork-card-body">
                            <h3 class="artwork-card-title">${artwork.title || 'Sans titre'}</h3>
                            <div class="artwork-card-artist">${artwork.artistDisplayName || 'Artiste inconnu'}</div>
                            <div class="artwork-card-date">${artwork.objectDate || 'Date inconnue'}</div>
                            ${artwork.medium ? `<div class="artwork-card-medium">${artwork.medium}</div>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            grid.innerHTML = '<div class="col-12 text-center text-white"><p>Aucune œuvre disponible</p></div>';
        }
    } catch (error) {
        console.error('Erreur chargement Met artworks:', error);
        grid.innerHTML = '<div class="col-12 text-center text-danger"><p>Erreur de chargement</p></div>';
    }
}

/**
 * Charge les collections Harvard
 */
async function loadHarvard() {
    const grid = document.getElementById('harvardGrid');
    const objectIds = [299843, 228640, 206055];
    
    try {
        const promises = objectIds.map(id => 
            fetch(`api/harvard-simple.php?id=${id}`).then(r => r.json())
        );
        
        const results = await Promise.all(promises);
        const validObjects = results.filter(r => r.success && r.object);
        
        if (validObjects.length > 0) {
            grid.innerHTML = validObjects.map(data => {
                const obj = data.object;
                return `
                    <div class="col-md-6 col-lg-4">
                        <div class="artwork-card">
                            <img src="${obj.primary_image || 'https://via.placeholder.com/400x300?text=Harvard+Collection'}" 
                                 alt="${obj.title || 'Sans titre'}" 
                                 class="artwork-card-img"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=Image+indisponible'">
                            <div class="artwork-card-body">
                                <h3 class="artwork-card-title">${obj.title || 'Sans titre'}</h3>
                                <div class="artwork-card-artist">${obj.culture || 'Culture inconnue'}</div>
                                ${obj.people && obj.people.length > 0 ? 
                                    `<div class="artwork-card-date">${obj.people[0].name || ''}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            grid.innerHTML = '<div class="col-12 text-center"><p>Aucune collection disponible</p></div>';
        }
    } catch (error) {
        console.error('Erreur chargement Harvard:', error);
        grid.innerHTML = '<div class="col-12 text-center text-danger"><p>Erreur de chargement</p></div>';
    }
}

/**
 * Charge la météo de plusieurs villes
 */
async function loadWeatherMultiple() {
    const grid = document.getElementById('weatherGrid');
    const cities = ['Paris', 'London', 'New York', 'Tokyo'];
    
    try {
        const promises = cities.map(city => 
            fetch(`api/weather-simple.php?city=${encodeURIComponent(city)}`).then(r => r.json())
        );
        
        const results = await Promise.all(promises);
        const validWeather = results.filter(r => r.success && r.weather);
        
        if (validWeather.length > 0) {
            grid.innerHTML = validWeather.map(data => {
                const w = data.weather;
                return `
                    <div class="col-md-6 col-lg-3">
                        <div class="weather-card">
                            <div class="weather-city">
                                <i class="fas fa-map-marker-alt me-2"></i>${w.city}
                            </div>
                            ${w.icon_url ? `<img src="${w.icon_url}" alt="Météo" style="width: 80px; height: 80px;">` : ''}
                            <div class="weather-temp">${Math.round(w.temperature)}°C</div>
                            ${w.description ? `<div class="weather-desc">${w.description}</div>` : ''}
                            <div class="weather-details">
                                <div class="weather-detail-item">
                                    <div class="weather-detail-value">
                                        <i class="fas fa-tint"></i> ${w.humidity}%
                                    </div>
                                    <div class="weather-detail-label">Humidité</div>
                                </div>
                                <div class="weather-detail-item">
                                    <div class="weather-detail-value">
                                        <i class="fas fa-wind"></i> ${w.wind_speed} m/s
                                    </div>
                                    <div class="weather-detail-label">Vent</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            grid.innerHTML = '<div class="col-12 text-center text-white"><p>Données météo indisponibles</p></div>';
        }
    } catch (error) {
        console.error('Erreur chargement météo:', error);
        grid.innerHTML = '<div class="col-12 text-center text-danger"><p>Erreur de chargement</p></div>';
    }
}

/**
 * Charge les localisations géographiques
 * FONCTION DÉSACTIVÉE - La carte a été déplacée vers musee-detail.php
 */
/* DÉSACTIVÉE
async function loadLocations() {
    const container = document.getElementById('locationsList');
    const locations = [
        'Louvre Museum, Paris, France',
        'British Museum, London, UK',
        'Metropolitan Museum of Art, New York, USA',
        'Rijksmuseum, Amsterdam, Netherlands'
    ];
    
    try {
        const promises = locations.map(loc => 
            fetch(`api/geocode.php?address=${encodeURIComponent(loc)}`).then(r => r.json())
        );
        
        const results = await Promise.all(promises);
        const validLocations = results.filter(r => r.success && r.latitude && r.longitude);
        
        if (validLocations.length > 0) {
            container.innerHTML = validLocations.map(data => `
                <div class="location-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="location-name">
                                <i class="fas fa-museum me-2" style="color: var(--secondary-color);"></i>
                                ${data.display_name?.split(',')[0] || 'Musée'}
                            </div>
                            <div class="location-coords">
                                <i class="fas fa-map-pin me-2"></i>
                                ${data.latitude.toFixed(6)}, ${data.longitude.toFixed(6)}
                            </div>
                            ${data.display_name ? `<div class="text-muted mt-2" style="font-size: 0.85rem;">${data.display_name}</div>` : ''}
                        </div>
                        <a href="https://www.openstreetmap.org/?mlat=${data.latitude}&mlon=${data.longitude}#map=15/${data.latitude}/${data.longitude}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Voir carte
                        </a>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<div class="text-center text-muted"><p>Aucune localisation disponible</p></div>';
        }
    } catch (error) {
        console.error('Erreur chargement géocodage:', error);
        container.innerHTML = '<div class="text-center text-danger"><p>Erreur de chargement</p></div>';
    }
}
*/

/**
 * Récupère les suggestions de recherche via Ajax
 */
async function fetchSearchSuggestions(query) {
    try {
        console.log('📡 Appel API:', `api/search-suggestions.php?q=${query}`);
        const response = await fetch(`api/search-suggestions.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        console.log('📦 Réponse API:', data);
        
        if (data.success && data.suggestions) {
            console.log(`✅ ${data.suggestions.length} suggestions trouvées`);
            displaySuggestions(data.suggestions);
        } else {
            console.warn('⚠️ Aucune suggestion');
            displaySuggestions([]);
        }
    } catch (error) {
        console.error('❌ Erreur suggestions:', error);
    }
}

/**
 * Affiche les suggestions de recherche
 */
function displaySuggestions(suggestions) {
    const container = document.getElementById('searchSuggestions');
    
    if (!container) {
        console.error('❌ Container searchSuggestions non trouvé');
        return;
    }
    
    console.log('🎨 Affichage des suggestions...');
    
    if (suggestions.length === 0) {
        container.innerHTML = `
            <div class="no-suggestions">
                <i class="fas fa-search"></i>
                <div>Aucun résultat trouvé</div>
            </div>
        `;
        container.classList.add('show');
        console.log('📭 Message "Aucun résultat"');
        return;
    }
    
    container.innerHTML = suggestions.map(item => `
        <div class="suggestion-item" 
             data-id="${item.id}"
             data-name="${item.name}"
             data-lat="${item.latitude || ''}"
             data-lng="${item.longitude || ''}">
            <div class="suggestion-content">
                <div class="suggestion-name">${item.name}</div>
                <div class="suggestion-location">${item.city}, ${item.country}</div>
            </div>
        </div>
    `).join('');
    
    container.classList.add('show');
    console.log('✅ Dropdown affiché avec classe "show"');
    
    // Ajouter les événements de clic
    container.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', function() {
            const museumId = this.dataset.id;
            const name = this.dataset.name;
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            
            console.log('🖱️ Clic sur musée ID:', museumId, name);
            
            // Remplir le champ de recherche
            document.getElementById('searchInput').value = name;
            
            // Charger UNIQUEMENT ce musée
            loadSpecificMuseum(museumId);
            
            // Centrer la carte sur le musée si coordonnées disponibles
            if (lat && lng && window.museumMap) {
                window.museumMap.setView([lat, lng], 15, {
                    animate: true,
                    duration: 1
                });
                
                // Ouvrir le popup du marker correspondant
                markersData.forEach(markerData => {
                    if (markerData.museum.id == museumId) {
                        markerData.marker.openPopup();
                    }
                });
            }
            
            // Cacher les suggestions
            hideSuggestions();
        });
    });
}

/**
 * Cache les suggestions
 */
function hideSuggestions() {
    const container = document.getElementById('searchSuggestions');
    container.classList.remove('show');
}

/**
 * CARTE DÉSACTIVÉE - Maintenant dans musee-detail.php
 * 
 * Initialise la carte Leaflet avec les musées
 */
let museumMap = null;
let museumMarkers = [];
let markersData = []; // Stocker les données des musées pour recherche

/* FONCTION DÉSACTIVÉE - La carte est maintenant dans les pages de détail
async function initMap() {
    // Créer la carte centrée sur l'Europe
    museumMap = L.map('museumMap').setView([48.8566, 2.3522], 6);
    window.museumMap = museumMap;
    
    // Ajouter le fond de carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(museumMap);
    
    // Icône personnalisée pour les musées
    const museumIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    
    // Charger TOUS les musées de la base de données
    try {
        console.log('🗺️ Chargement de tous les musées...');
        const response = await fetch('api/museums-search.php?category=all&limit=1000');
        const data = await response.json();
        
        if (data.success && data.museums) {
            console.log(`📍 ${data.museums.length} musées reçus de l'API`);
            
            let addedCount = 0;
            data.museums.forEach(museum => {
                if (museum.latitude && museum.longitude) {
                    const lat = parseFloat(museum.latitude);
                    const lng = parseFloat(museum.longitude);
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const marker = L.marker([lat, lng], {
                            icon: museumIcon
                        })
                            .bindPopup(`
                                <div class="museum-popup">
                                    <div class="popup-name">${museum.name}</div>
                                    <div class="popup-address">
                                        <i class="fas fa-map-marker-alt"></i>
                                        ${museum.city}, ${museum.country}
                                    </div>
                                    ${museum.address ? `<div class="popup-address">${museum.address}</div>` : ''}
                                    <a href="musee-detail.php?id=${museum.id}" 
                                       class="btn btn-sm btn-gradient popup-btn mt-2">
                                        <i class="fas fa-eye me-1"></i> Voir détails
                                    </a>
                                </div>
                            `)
                            .addTo(museumMap);
                        
                        // Stocker le marker avec ses données
                        museumMarkers.push(marker);
                        markersData.push({
                            marker: marker,
                            museum: museum
                        });
                        addedCount++;
                    }
                }
            });
            
            console.log(`✅ ${addedCount} markers ajoutés à la carte`);
            
            // Ajuster la vue pour montrer tous les marqueurs
            if (museumMarkers.length > 0) {
                const group = L.featureGroup(museumMarkers);
                museumMap.fitBounds(group.getBounds().pad(0.1));
                console.log('🌍 Vue ajustée pour afficher tous les musées');
            }
        } else {
            console.error('❌ Erreur API:', data);
        }
    } catch (error) {
        console.error('❌ Erreur chargement carte:', error);
        // Afficher le message d'erreur à l'utilisateur
        const mapElement = document.getElementById('museumMap');
        if (mapElement) {
            const errorDiv = document.createElement('div');
            errorDiv.style.cssText = 'position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1000;text-align:center;';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#f59e0b;font-size:32px;"></i><br><strong>Erreur de chargement</strong><br><span style="color:#64748b;font-size:14px;">Impossible de charger les musées</span>';
            mapElement.appendChild(errorDiv);
        }
    }
}
*/

/**
 * Initialise toutes les APIs au chargement
 */
document.addEventListener('DOMContentLoaded', function() {
    // Charger toutes les nouvelles APIs
    loadMetArtworks();
    loadHarvard();
    loadWeatherMultiple();
    
    // Event listener pour le bouton "Découvrir plus d'œuvres"
    document.getElementById('loadMoreMet')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Chargement...';
        loadMetArtworks();
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync-alt me-2"></i> Découvrir plus d\'œuvres';
        }, 1000);
    });
});

/**
 * Custom country dropdown handler
 */
function initCustomCountryDropdown() {
    const trigger = document.getElementById('countryTrigger');
    const dropdown = document.getElementById('countryDropdown');
    const options = document.querySelectorAll('.country-option');
    const hiddenSelect = document.getElementById('countryFilter');
    
    if (!trigger || !dropdown) return;
    
    // Toggle dropdown on trigger click
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        dropdown.classList.remove('show');
    });
    
    // Handle option selection
    options.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const text = this.textContent;
            
            // Update trigger text
            trigger.textContent = text;
            
            // Update hidden select
            if (hiddenSelect) {
                hiddenSelect.value = value;
            }
            
            // Update visual state
            options.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            // Close dropdown
            dropdown.classList.remove('show');
            
            // Trigger search
            state.currentCountry = value;
            state.currentPage = 1;
            loadMuseums();
        });
    });
}
