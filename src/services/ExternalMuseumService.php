<?php

/**
 * Service pour appeler les APIs externes de musées
 * 
 * Ce service gère :
 * - Appels aux APIs Harvard, Met Museum, Rijksmuseum
 * - Mise en cache des résultats
 * - Géocodage des adresses
 * - Météo
 */
class ExternalMuseumService {
    private $pdo;
    private $apiKeys;
    private $cacheDuration = 86400; // 24 heures
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        // Charger les clés API
        if (file_exists(__DIR__ . '/../../secret/api_keys.php')) {
            $this->apiKeys = require __DIR__ . '/../../secret/api_keys.php';
        } else {
            $this->apiKeys = require __DIR__ . '/../../config/api_keys.php';
        }
    }
    
    /**
     * Récupère les détails d'un musée depuis l'API externe avec cache
     * 
     * @param int $museumId ID du musée dans notre BDD
     * @param string $apiSource Source de l'API (harvard, met, rijksmuseum)
     * @return array|null Données du musée
     */
    public function getMuseumDetails($museumId, $apiSource) {
        // 1. Vérifier le cache
        $cached = $this->getCachedData('museum_api_cache', $museumId, $apiSource);
        if ($cached) {
            return json_decode($cached['data'], true);
        }
        
        // 2. Appeler l'API externe
        $data = null;
        switch ($apiSource) {
            case 'harvard':
                $data = $this->fetchHarvardMuseum($museumId);
                break;
            case 'met':
                $data = $this->fetchMetMuseum($museumId);
                break;
            case 'rijksmuseum':
                $data = $this->fetchRijksmuseum($museumId);
                break;
            default:
                return null;
        }
        
        // 3. Mettre en cache si succès
        if ($data) {
            $this->cacheData('museum_api_cache', $museumId, $apiSource, $data);
        }
        
        return $data;
    }
    
    /**
     * Récupère les œuvres d'art d'un musée en combinant plusieurs APIs
     * 
     * @param int $museumId ID du musée
     * @param string $apiSource Source de l'API (ou 'multi' pour combiner)
     * @param int $limit Nombre d'œuvres max
     * @return array Liste des œuvres
     */
    public function getMuseumArtworks($museumId, $apiSource, $limit = 20) {
        // 1. Vérifier le cache
        $cached = $this->getCachedArtworks($museumId);
        if ($cached) {
            $artworks = json_decode($cached['artworks'], true);
            return array_slice($artworks, 0, $limit);
        }
        
        // 2. Récupérer les œuvres depuis plusieurs APIs pour maximiser les résultats
        $allArtworks = [];
        
        // Stratégie : essayer Met Museum d'abord, puis compléter avec Harvard et Chicago
        $metArtworks = $this->fetchMetArtworks($museumId, ceil($limit * 0.6)); // 60% depuis Met
        $allArtworks = array_merge($allArtworks, $metArtworks);
        
        // Compléter avec Harvard si besoin
        if (count($allArtworks) < $limit) {
            $remaining = $limit - count($allArtworks);
            $harvardArtworks = $this->fetchHarvardArtworks($museumId, $remaining);
            $allArtworks = array_merge($allArtworks, $harvardArtworks);
        }
        
        // Compléter avec Chicago si besoin
        if (count($allArtworks) < $limit) {
            $remaining = $limit - count($allArtworks);
            $chicagoArtworks = $this->fetchChicagoArtworks($museumId, $remaining);
            $allArtworks = array_merge($allArtworks, $chicagoArtworks);
        }
        
        // 3. Normaliser les données pour avoir un format uniforme
        $normalizedArtworks = $this->normalizeArtworks($allArtworks);
        
        // 4. Mettre en cache
        if (!empty($normalizedArtworks)) {
            $this->cacheArtworks($museumId, 'multi', $normalizedArtworks);
        }
        
        return array_slice($normalizedArtworks, 0, $limit);
    }
    
    /**
     * Géocode une adresse pour obtenir lat/lng
     * 
     * @param string $address Adresse complète
     * @return array|null ['lat' => float, 'lng' => float]
     */
    public function geocodeAddress($address) {
        // 1. Vérifier le cache
        $stmt = $this->pdo->prepare("
            SELECT latitude, longitude 
            FROM geocode_cache 
            WHERE address = ?
        ");
        $stmt->execute([$address]);
        $cached = $stmt->fetch();
        
        if ($cached) {
            return [
                'lat' => (float)$cached['latitude'],
                'lng' => (float)$cached['longitude']
            ];
        }
        
        // 2. Appeler Nominatim (OpenStreetMap)
        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
            'q' => $address,
            'format' => 'json',
            'limit' => 1
        ]);
        
        $response = $this->makeRequest($url, [
            'User-Agent: MuseeExplorer/1.0 (Educational Project)'
        ]);
        
        if ($response && !empty($response[0])) {
            $coords = [
                'lat' => (float)$response[0]['lat'],
                'lng' => (float)$response[0]['lon']
            ];
            
            // 3. Mettre en cache
            $stmt = $this->pdo->prepare("
                INSERT INTO geocode_cache (address, latitude, longitude, fetched_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    latitude = VALUES(latitude),
                    longitude = VALUES(longitude),
                    fetched_at = NOW()
            ");
            $stmt->execute([$address, $coords['lat'], $coords['lng']]);
            
            return $coords;
        }
        
        return null;
    }
    
    /**
     * Récupère la météo pour une ville
     * 
     * @param string $city Nom de la ville
     * @return array|null Données météo
     */
    public function getWeather($city) {
        // 1. Vérifier le cache (expire après 1h)
        $stmt = $this->pdo->prepare("
            SELECT weather_data 
            FROM weather_cache 
            WHERE city = ? AND expires_at > NOW()
        ");
        $stmt->execute([$city]);
        $cached = $stmt->fetch();
        
        if ($cached) {
            return json_decode($cached['weather_data'], true);
        }
        
        // 2. Appeler OpenWeather API
        $apiKey = $this->apiKeys['OPENWEATHER_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return null;
        }
        
        $url = "https://api.openweathermap.org/data/2.5/weather?" . http_build_query([
            'q' => $city,
            'appid' => $apiKey,
            'units' => 'metric',
            'lang' => 'fr'
        ]);
        
        $data = $this->makeRequest($url);
        
        // 3. Mettre en cache (1 heure)
        if ($data) {
            $stmt = $this->pdo->prepare("
                INSERT INTO weather_cache (city, weather_data, fetched_at, expires_at)
                VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR))
                ON DUPLICATE KEY UPDATE 
                    weather_data = VALUES(weather_data),
                    fetched_at = NOW(),
                    expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$city, json_encode($data)]);
        }
        
        return $data;
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - HARVARD ART MUSEUMS
    // ========================================
    
    private function fetchHarvardMuseum($museumId) {
        $apiKey = $this->apiKeys['HARVARD_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return null;
        }
        
        // Harvard : récupérer une galerie ou un objet
        $url = "https://api.harvardartmuseums.org/object/{$museumId}?apikey={$apiKey}";
        return $this->makeRequest($url);
    }
    
    private function fetchHarvardArtworks($museumId, $limit) {
        $apiKey = $this->apiKeys['HARVARD_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return [];
        }
        
        $url = "https://api.harvardartmuseums.org/object?" . http_build_query([
            'gallery' => $museumId,
            'size' => $limit,
            'apikey' => $apiKey,
            'hasimage' => 1
        ]);
        
        $response = $this->makeRequest($url);
        return $response['records'] ?? [];
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - MET MUSEUM
    // ========================================
    
    private function fetchMetMuseum($museumId) {
        $url = "https://collectionapi.metmuseum.org/public/collection/v1/objects/{$museumId}";
        return $this->makeRequest($url);
    }
    
    private function fetchMetArtworks($museumId, $limit) {
        // Recherche générale d'œuvres avec images (pas de département spécifique)
        // Utiliser des termes de recherche populaires pour obtenir des résultats intéressants
        $searchTerms = ['painting', 'sculpture', 'portrait', 'landscape', 'impressionism'];
        $randomTerm = $searchTerms[array_rand($searchTerms)];
        
        $url = "https://collectionapi.metmuseum.org/public/collection/v1/search?" . http_build_query([
            'hasImages' => 'true',
            'q' => $randomTerm
        ]);
        
        $response = $this->makeRequest($url);
        
        if (!$response || empty($response['objectIDs'])) {
            // Essayer avec une recherche plus générique
            $url = "https://collectionapi.metmuseum.org/public/collection/v1/search?" . http_build_query([
                'hasImages' => 'true',
                'q' => 'art'
            ]);
            $response = $this->makeRequest($url);
            
            if (!$response || empty($response['objectIDs'])) {
                return [];
            }
        }
        
        // Récupérer les détails des N premiers objets
        $artworks = [];
        $objectIds = array_slice($response['objectIDs'], 0, min($limit * 2, 20)); // Demander plus car certains peuvent échouer
        
        foreach ($objectIds as $objectId) {
            if (count($artworks) >= $limit) break; // Arrêter si on a assez d'œuvres
            
            $artwork = $this->makeRequest(
                "https://collectionapi.metmuseum.org/public/collection/v1/objects/{$objectId}"
            );
            
            if ($artwork && !empty($artwork['primaryImageSmall'])) {
                $artworks[] = $artwork;
            }
            
            // Pause pour respecter les limites de l'API
            usleep(150000); // 0.15 seconde
        }
        
        return $artworks;
    }
    
    /**
     * Récupère les œuvres d'art depuis Chicago Art Institute API
     */
    private function fetchChicagoArtworks($museumId, $limit) {
        $url = "https://api.artic.edu/api/v1/artworks/search?" . http_build_query([
            'q' => 'painting',
            'limit' => min($limit, 20),
            'fields' => 'id,title,artist_display,date_display,image_id,thumbnail'
        ]);
        
        $response = $this->makeRequest($url);
        
        if (!$response || empty($response['data'])) {
            return [];
        }
        
        $artworks = [];
        foreach ($response['data'] as $item) {
            if (!empty($item['image_id'])) {
                $artworks[] = [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? 'Sans titre',
                    'artistDisplayName' => $item['artist_display'] ?? 'Artiste inconnu',
                    'objectDate' => $item['date_display'] ?? '',
                    'primaryImage' => "https://www.artic.edu/iiif/2/{$item['image_id']}/full/843,/0/default.jpg",
                    'primaryImageSmall' => "https://www.artic.edu/iiif/2/{$item['image_id']}/full/400,/0/default.jpg",
                    'source' => 'chicago'
                ];
            }
        }
        
        return $artworks;
    }
    
    /**
     * Normalise les œuvres de différentes APIs pour avoir un format uniforme
     */
    private function normalizeArtworks($artworks) {
        $normalized = [];
        
        foreach ($artworks as $artwork) {
            // Détecter la source et normaliser
            if (isset($artwork['source'])) {
                // Déjà normalisé (Chicago)
                $normalized[] = $artwork;
            } elseif (isset($artwork['primaryImageSmall'])) {
                // Format Met Museum
                $normalized[] = [
                    'id' => $artwork['objectID'] ?? $artwork['id'] ?? uniqid(),
                    'title' => $artwork['title'] ?? 'Sans titre',
                    'artistDisplayName' => $artwork['artistDisplayName'] ?? 'Artiste inconnu',
                    'objectDate' => $artwork['objectDate'] ?? '',
                    'primaryImage' => $artwork['primaryImage'] ?? '',
                    'primaryImageSmall' => $artwork['primaryImageSmall'] ?? '',
                    'source' => 'met'
                ];
            } elseif (isset($artwork['primaryimageurl'])) {
                // Format Harvard
                $normalized[] = [
                    'id' => $artwork['id'] ?? uniqid(),
                    'title' => $artwork['title'] ?? 'Sans titre',
                    'artistDisplayName' => $artwork['people'][0]['name'] ?? 'Artiste inconnu',
                    'objectDate' => $artwork['dated'] ?? '',
                    'primaryImage' => $artwork['primaryimageurl'] ?? '',
                    'primaryImageSmall' => $artwork['primaryimageurl'] ?? '',
                    'source' => 'harvard'
                ];
            }
        }
        
        return $normalized;
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - RIJKSMUSEUM
    // ========================================
    
    private function fetchRijksmuseum($museumId) {
        $apiKey = $this->apiKeys['RIJKSMUSEUM_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return null;
        }
        
        $url = "https://www.rijksmuseum.nl/api/nl/collection/{$museumId}?key={$apiKey}";
        return $this->makeRequest($url);
    }
    
    private function fetchRijksmuseumArtworks($museumId, $limit) {
        $apiKey = $this->apiKeys['RIJKSMUSEUM_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return [];
        }
        
        $url = "https://www.rijksmuseum.nl/api/nl/collection?" . http_build_query([
            'key' => $apiKey,
            'ps' => $limit,
            'imgonly' => 'True'
        ]);
        
        $response = $this->makeRequest($url);
        return $response['artObjects'] ?? [];
    }
    
    // ========================================
    // GESTION DU CACHE
    // ========================================
    
    private function getCachedData($table, $museumId, $apiSource) {
        $stmt = $this->pdo->prepare("
            SELECT data 
            FROM {$table}
            WHERE museum_id = ? 
            AND api_source = ? 
            AND expires_at > NOW()
        ");
        $stmt->execute([$museumId, $apiSource]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getCachedArtworks($museumId) {
        $stmt = $this->pdo->prepare("
            SELECT artworks 
            FROM artwork_cache
            WHERE museum_id = ? 
            AND expires_at > NOW()
        ");
        $stmt->execute([$museumId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function cacheData($table, $museumId, $apiSource, $data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$table} (museum_id, api_source, data, fetched_at, expires_at)
            VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))
            ON DUPLICATE KEY UPDATE
                data = VALUES(data),
                fetched_at = NOW(),
                expires_at = VALUES(expires_at)
        ");
        $stmt->execute([$museumId, $apiSource, json_encode($data), $this->cacheDuration]);
    }
    
    private function cacheArtworks($museumId, $apiSource, $artworks) {
        $stmt = $this->pdo->prepare("
            INSERT INTO artwork_cache (museum_id, api_source, artworks, total_count, fetched_at, expires_at)
            VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))
            ON DUPLICATE KEY UPDATE
                artworks = VALUES(artworks),
                total_count = VALUES(total_count),
                fetched_at = NOW(),
                expires_at = VALUES(expires_at)
        ");
        $stmt->execute([
            $museumId, 
            $apiSource, 
            json_encode($artworks), 
            count($artworks), 
            $this->cacheDuration
        ]);
    }
    
    // ========================================
    // HTTP REQUEST
    // ========================================
    
    private function makeRequest($url, $headers = []) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        // Log l'erreur si besoin
        error_log("API Request Failed - URL: {$url}, HTTP: {$httpCode}, Error: {$error}");
        
        return null;
    }
}
