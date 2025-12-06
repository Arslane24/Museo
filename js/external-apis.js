/**
 * API EXTERNES - Appels directs depuis JavaScript
 * Ce fichier contient les fonctions pour appeler les APIs externes
 * (Met Museum, OpenWeather, etc.) directement depuis le navigateur
 */

// ====================================================
// CONFIGURATION DES APIS EXTERNES
// ====================================================

const API_KEYS = {
    OPENWEATHER: '09bb84206c31c4428d3df828199144fb',
    HARVARD_ART: '6ec8530d-1428-4f48-91b9-4d27b4eaf4d1',
    EUROPEANA: 'addevollail'
};

const API_URLS = {
    MET_MUSEUM: 'https://collectionapi.metmuseum.org/public/collection/v1/',
    HARVARD_ART: 'https://api.harvardartmuseums.org/',
    CHICAGO_ART: 'https://api.artic.edu/',
    OPENWEATHER: 'https://api.openweathermap.org/data/2.5/',
    NOMINATIM: 'https://nominatim.openstreetmap.org/search',
    EUROPEANA: 'https://api.europeana.eu/record/v2/',
    WIKIDATA: 'https://query.wikidata.org/sparql'
};

// ====================================================
// FONCTIONS ŒUVRES D'ART
// ====================================================

/**
 * Récupère les œuvres d'art en combinant toutes les APIs disponibles
 * @param {string} museumName - Nom du musée pour la recherche
 * @param {number} limit - Nombre total d'œuvres à récupérer
 * @returns {Promise<Array>} Liste des œuvres combinées
 */
async function getCombinedArtworks(museumName, limit = 20) {
    try {
        console.log(`Chargement œuvres pour: "${museumName}" (max ${limit})...`);
        
        const allArtworks = [];
        const perAPI = 8;
        
        // Charger TOUTES les APIs en parallèle avec le nom du musée
        console.log('⏳ Chargement depuis 5 APIs en parallèle...');
        const promises = [
            getMetMuseumArtworks(museumName, perAPI).catch(() => []),
            getChicagoArtworks(museumName, perAPI).catch(() => []),
            getHarvardArtworks(museumName, perAPI).catch(() => []),
            getEuropeanaArtworks(museumName, perAPI).catch(() => []),
            getWikidataArtworks(museumName, perAPI).catch(() => [])
        ];
        
        const results = await Promise.all(promises);
        results.forEach(artworks => allArtworks.push(...artworks));
        
        console.log(`📊 Total brut: ${allArtworks.length} œuvres collectées`);
        
        // PRIORISER les œuvres avec images
        const withImages = allArtworks.filter(a => a.hasImage);
        const withoutImages = allArtworks.filter(a => !a.hasImage);
        
        // Combiner: d'abord celles avec images, puis sans images
        const sorted = [...withImages, ...withoutImages];
        
        // Limiter au maximum demandé
        const final = sorted.slice(0, limit);
        
        console.log(`Total final: ${final.length} œuvres (${withImages.length} avec images)`);
        return final;
        
    } catch (error) {
        console.error('❌ Erreur recherche combinée:', error);
        return [];
    }
}

/**
 * Récupère les œuvres d'art depuis Met Museum API
 * @param {string} searchTerm - Terme de recherche (ex: "painting", "sculpture")
 * @param {number} limit - Nombre d'œuvres à récupérer
 * @returns {Promise<Array>} Liste des œuvres
 */
async function getMetMuseumArtworks(searchTerm = 'painting', limit = 20) {
    try {
        console.log(`Chargement œuvres Met Museum: "${searchTerm}"`);
        
        // 1. Recherche des IDs d'œuvres (SANS filtre hasImages pour garder toutes les œuvres)
        const searchUrl = `${API_URLS.MET_MUSEUM}search?q=${encodeURIComponent(searchTerm)}`;
        const searchResponse = await fetch(searchUrl);
        const searchData = await searchResponse.json();
        
        if (!searchData.objectIDs || searchData.objectIDs.length === 0) {
            console.warn('Aucune œuvre trouvée');
            return [];
        }
        
        // 2. Récupérer les détails des premières œuvres
        const objectIds = searchData.objectIDs.slice(0, Math.min(limit + 10, 30));
        const artworks = [];
        
        for (const objectId of objectIds) {
            if (artworks.length >= limit) break;
            
            try {
                const detailUrl = `${API_URLS.MET_MUSEUM}objects/${objectId}`;
                const detailResponse = await fetch(detailUrl);
                const artwork = await detailResponse.json();
                
                // PRIORISER les œuvres avec images
                if (artwork.primaryImageSmall) {
                    artworks.push({
                        id: artwork.objectID,
                        title: artwork.title || 'Sans titre',
                        artist: artwork.artistDisplayName || 'Artiste inconnu',
                        date: artwork.objectDate || '',
                        image: artwork.primaryImageSmall || null,
                        imageUrl: artwork.primaryImage || null,
                        hasImage: true,
                        source: 'Met Museum'
                    });
                }
            } catch (err) {
                console.error(`Erreur chargement œuvre ${objectId}:`, err);
            }
            
            // Pause minimale pour accélérer
            await sleep(20);
        }
        
        console.log(`${artworks.length} œuvres chargées depuis Met Museum`);
        return artworks;
        
    } catch (error) {
        console.error('❌ Erreur Met Museum API:', error);
        return [];
    }
}

/**
 * Récupère les œuvres d'art depuis Harvard Art Museums API
 * @param {string} searchTerm - Terme de recherche
 * @param {number} limit - Nombre d'œuvres à récupérer
 * @returns {Promise<Array>} Liste des œuvres
 */
async function getHarvardArtworks(searchTerm = 'art', limit = 10) {
    try {
        console.log(`Chargement œuvres Harvard Art Museums: "${searchTerm}"`);
        
        // Recherche avec keyword (enlever hasimage pour garder toutes les œuvres)
        const url = `${API_URLS.HARVARD_ART}object?apikey=${API_KEYS.HARVARD_ART}&keyword=${encodeURIComponent(searchTerm)}&size=${limit}`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (!data.records || data.records.length === 0) {
            console.warn('Aucune œuvre trouvée');
            return [];
        }
        
        const artworks = data.records.map(artwork => ({
            id: artwork.id,
            title: artwork.title || 'Sans titre',
            artist: artwork.people?.[0]?.name || 'Artiste inconnu',
            date: artwork.dated || '',
            image: artwork.primaryimageurl || null,
            imageUrl: artwork.primaryimageurl || null,
            hasImage: !!artwork.primaryimageurl,
            source: 'Harvard Art Museums'
        }));
        
        console.log(`${artworks.length} œuvres chargées depuis Harvard`);
        return artworks;
        
    } catch (error) {
        console.error('❌ Erreur Harvard Art API:', error);
        return [];
    }
}

/**
 * Récupère les œuvres d'art depuis Chicago Art Institute API
 * @param {string} searchTerm - Terme de recherche
 * @param {number} limit - Nombre d'œuvres à récupérer
 * @returns {Promise<Array>} Liste des œuvres
 */
async function getChicagoArtworks(searchTerm = 'painting', limit = 10) {
    try {
        console.log(`Chargement œuvres Chicago Art Institute: "${searchTerm}"`);
        
        const url = `${API_URLS.CHICAGO_ART}api/v1/artworks/search?q=${encodeURIComponent(searchTerm)}&limit=${limit}&fields=id,title,artist_display,date_display,image_id`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (!data.data || data.data.length === 0) {
            console.warn('Aucune œuvre trouvée');
            return [];
        }
        
        // Garder TOUTES les œuvres, même sans image
        const artworks = data.data.map(item => ({
            id: item.id,
            title: item.title || 'Sans titre',
            artist: item.artist_display || 'Artiste inconnu',
            date: item.date_display || '',
            image: item.image_id ? `https://www.artic.edu/iiif/2/${item.image_id}/full/400,/0/default.jpg` : null,
            imageUrl: item.image_id ? `https://www.artic.edu/iiif/2/${item.image_id}/full/843,/0/default.jpg` : null,
            hasImage: !!item.image_id,
            source: 'Chicago Art Institute'
        }));
        
        console.log(`${artworks.length} œuvres chargées depuis Chicago`);
        return artworks;
        
    } catch (error) {
        console.error('❌ Erreur Chicago Art API:', error);
        return [];
    }
}

// ====================================================
// FONCTION MÉTÉO
// ====================================================

/**
 * Récupère la météo d'une ville via OpenWeather API
 * @param {string} city - Nom de la ville
 * @returns {Promise<Object>} Données météo
 */
async function getWeather(city) {
    try {
        console.log(`🌤️ Chargement météo pour: ${city}`);
        
        const url = `${API_URLS.OPENWEATHER}weather?q=${encodeURIComponent(city)}&appid=${API_KEYS.OPENWEATHER}&units=metric&lang=fr`;
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        const weather = {
            temperature: Math.round(data.main.temp),
            feels_like: Math.round(data.main.feels_like),
            description: data.weather[0].description,
            icon: data.weather[0].icon,
            humidity: data.main.humidity,
            wind_speed: Math.round(data.wind.speed * 3.6), // m/s vers km/h
            city: data.name
        };
        
        console.log(`Météo chargée:`, weather);
        return weather;
        
    } catch (error) {
        console.error('❌ Erreur OpenWeather API:', error);
        return null;
    }
}

/**
 * Obtient l'icône météo appropriée
 * @param {string} iconCode - Code icône OpenWeather
 * @returns {string} URL de l'icône
 */
function getWeatherIconUrl(iconCode) {
    return `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
}

// ====================================================
// FONCTIONS EUROPEANA & WIKIDATA
// ====================================================

/**
 * Récupère les œuvres depuis Europeana API
 * @param {string} searchTerm - Terme de recherche
 * @param {number} limit - Nombre d'œuvres
 * @returns {Promise<Array>} Liste des œuvres
 */
async function getEuropeanaArtworks(searchTerm = 'art', limit = 10) {
    try {
        console.log(`🇪🇺 Chargement œuvres Europeana: "${searchTerm}"`);
        
        const url = `${API_URLS.EUROPEANA}search.json?wskey=${API_KEYS.EUROPEANA}&query=${encodeURIComponent(searchTerm)}&rows=${limit}&profile=rich`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (!data.items || data.items.length === 0) {
            console.warn('Aucune œuvre trouvée sur Europeana');
            return [];
        }
        
        const artworks = data.items.map(item => ({
            id: item.id,
            title: Array.isArray(item.title) ? item.title[0] : item.title || 'Sans titre',
            artist: Array.isArray(item.dcCreator) ? item.dcCreator[0] : item.dcCreator || 'Artiste inconnu',
            date: Array.isArray(item.year) ? item.year[0] : item.year || '',
            image: Array.isArray(item.edmPreview) ? item.edmPreview[0] : item.edmPreview || null,
            imageUrl: Array.isArray(item.edmPreview) ? item.edmPreview[0] : item.edmPreview || null,
            hasImage: !!(Array.isArray(item.edmPreview) ? item.edmPreview[0] : item.edmPreview),
            type: item.type || '',
            description: Array.isArray(item.dcDescription) ? item.dcDescription[0] : item.dcDescription || '',
            source: 'Europeana',
            link: item.guid
        }));
        
        console.log(`${artworks.length} œuvres chargées depuis Europeana`);
        return artworks;
        
    } catch (error) {
        console.error('❌ Erreur Europeana API:', error);
        return [];
    }
}

/**
 * Récupère les œuvres depuis Wikidata API
 * @param {string} searchTerm - Terme de recherche (nom du musée)
 * @param {number} limit - Nombre d'œuvres
 * @returns {Promise<Array>} Liste des œuvres
 */
async function getWikidataArtworks(searchTerm = 'art', limit = 10) {
    try {
        console.log(`📚 Chargement œuvres Wikidata: "${searchTerm}"`);
        
        // Query SPARQL pour chercher des œuvres liées au terme
        const sparqlQuery = `
            SELECT ?artwork ?artworkLabel ?creatorLabel ?image ?inception ?description WHERE {
              ?artwork rdfs:label ?label .
              FILTER(CONTAINS(LCASE(?label), "${searchTerm.toLowerCase()}"))
              ?artwork wdt:P31/wdt:P279* wd:Q838948 .
              OPTIONAL { ?artwork wdt:P170 ?creator }
              OPTIONAL { ?artwork wdt:P18 ?image }
              OPTIONAL { ?artwork wdt:P571 ?inception }
              OPTIONAL { ?artwork schema:description ?description FILTER(LANG(?description) = "fr") }
              SERVICE wikibase:label { bd:serviceParam wikibase:language "fr,en". }
            }
            LIMIT ${limit}
        `;
        
        const url = `${API_URLS.WIKIDATA}?query=${encodeURIComponent(sparqlQuery)}&format=json`;
        const response = await fetch(url, {
            headers: { 'Accept': 'application/sparql-results+json' }
        });
        const data = await response.json();
        
        if (!data.results.bindings || data.results.bindings.length === 0) {
            console.warn('Aucune œuvre trouvée sur Wikidata');
            return [];
        }
        
        const artworks = data.results.bindings.map(item => ({
            id: item.artwork?.value.split('/').pop(),
            title: item.artworkLabel?.value || 'Sans titre',
            artist: item.creatorLabel?.value || 'Artiste inconnu',
            date: item.inception?.value?.substring(0, 4) || '',
            image: item.image?.value || null,
            imageUrl: item.image?.value || null,
            hasImage: !!item.image?.value,
            description: item.description?.value || '',
            source: 'Wikidata',
            link: item.artwork?.value
        }));
        
        console.log(`${artworks.length} œuvres chargées depuis Wikidata`);
        return artworks;
        
    } catch (error) {
        console.error('❌ Erreur Wikidata API:', error);
        return [];
    }
}

// ====================================================
// FONCTION GÉOCODAGE
// ====================================================

/**
 * Géocode une adresse via Nominatim (OpenStreetMap)
 * @param {string} address - Adresse complète
 * @returns {Promise<Object>} Coordonnées {lat, lon}
 */
async function geocodeAddress(address) {
    try {
        console.log(`📍 Géocodage de: ${address}`);
        
        const url = `${API_URLS.NOMINATIM}?q=${encodeURIComponent(address)}&format=json&limit=1`;
        const response = await fetch(url, {
            headers: {
                'User-Agent': 'MuseoLink/1.0 (Educational Project)'
            }
        });
        
        const data = await response.json();
        
        if (!data || data.length === 0) {
            console.warn('Adresse introuvable');
            return null;
        }
        
        const coords = {
            lat: parseFloat(data[0].lat),
            lon: parseFloat(data[0].lon)
        };
        
        console.log(`Coordonnées trouvées:`, coords);
        return coords;
        
    } catch (error) {
        console.error('❌ Erreur géocodage:', error);
        return null;
    }
}

// ====================================================
// FONCTIONS UTILITAIRES
// ====================================================

/**
 * Pause l'exécution pendant X millisecondes
 * @param {number} ms - Millisecondes
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Formatte une température
 * @param {number} temp - Température
 * @returns {string} Température formatée
 */
function formatTemperature(temp) {
    return `${Math.round(temp)}°C`;
}

/**
 * Obtient l'emoji météo selon la description
 * @param {string} description - Description météo
 * @returns {string} Emoji
 */
function getWeatherEmoji(description) {
    const desc = description.toLowerCase();
    if (desc.includes('soleil') || desc.includes('clair')) return '☀️';
    if (desc.includes('nuage')) return '☁️';
    if (desc.includes('pluie')) return '🌧️';
    if (desc.includes('orage')) return '⛈️';
    if (desc.includes('neige')) return '❄️';
    if (desc.includes('brouillard')) return '🌫️';
    return '🌤️';
}

// ====================================================
// EXPORT DES FONCTIONS
// ====================================================

// Les fonctions sont disponibles globalement
console.log('Module APIs Externes chargé');
