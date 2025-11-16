<?php
// Configuration des clés API - MUSEO
// 10 APIs au total

// ========================================
// APIs D'ŒUVRES D'ART FRANÇAISES (3 APIs)
// ========================================
define('EUROPEANA_API_URL', 'https://www.europeana.eu/api/v2/search.json');
define('EUROPEANA_API_KEY', 'addevollail');

define('PARIS_MUSEES_API_URL', 'https://www.parismuseescollections.paris.fr/api/');
// Pas de clé requise pour Paris Musées

define('IMAGES_ART_API_URL', 'https://art.rmngp.fr/api/');
// Pas de clé requise pour Images d'Art

// ========================================
// APIs D'ŒUVRES D'ART INTERNATIONALES (3 APIs)
// ========================================
define('MET_MUSEUM_API_URL', 'https://collectionapi.metmuseum.org/public/collection/v1/');
// Pas de clé requise pour Met Museum

define('HARVARD_ART_API_URL', 'https://api.harvardartmuseums.org/');
define('HARVARD_ART_API_KEY', '6ec8530d-1428-4f48-91b9-4d27b4eaf4d1'); // Clé fournie pour tests

define('CHICAGO_ART_API_URL', 'https://api.artic.edu/');
// Pas de clé requise pour Chicago Art Institute

// ========================================
// APIs DE DONNÉES STRUCTURÉES (2 APIs)
// ========================================
define('WIKIDATA_API_URL', 'https://www.wikidata.org/w/api.php');
// Pas de clé requise pour Wikidata

define('DBPEDIA_API_URL', 'https://dbpedia.org/sparql');
// Pas de clé requise pour DBpedia

// ========================================
// APIs DE LOCALISATION (2 APIs)
// ========================================
define('NOMINATIM_API_URL', 'https://nominatim.openstreetmap.org/search');
define('OPENCAGE_API_URL', 'https://api.opencagedata.com/geocode/v1/json');
define('OPENCAGE_API_KEY', '56240991f2b34462b6f0caf6bdd0830e');

// ========================================
// APIs DE MÉTÉO (1 API)
// ========================================
define('OPENWEATHER_API_URL', 'https://api.openweathermap.org/data/2.5/');
define('OPENWEATHER_API_KEY', '09bb84206c31c4428d3df828199144fb');

// ========================================
// APIs MUSEES INTERNATIONAUX (4 APIs SANS CLÉ)
// ========================================
define('RIJKSMUSEUM_API_URL', 'https://www.rijksmuseum.nl/api/nl/collection');
define('BRITISH_MUSEUM_API_URL', 'https://www.britishmuseum.org/api/');
define('WIKIMEDIA_COMMONS_API_URL', 'https://commons.wikimedia.org/w/api.php');
define('POP_API_URL', 'https://data.culture.gouv.fr/api/');

// Smithsonian avec clé
define('SMITHSONIAN_API_URL', 'https://api.si.edu/openaccess/');
define('SMITHSONIAN_API_KEY', 'K4Q4bTWIyN4AUALW8vngTuTLh1JU8gk19EdbX2Q4');

// Configuration de base
define('SITE_NAME', 'MUSEO');
define('SITE_URL', 'https://museo.alwaysdata.net/');
define('CURRENCY', 'EUR');

// Configuration des APIs enrichies - 10 APIs au total (Harvard désactivée)
define('MUSEO_APIS', [
    // Œuvres d'art françaises
    'europeana' => [
        'url' => EUROPEANA_API_URL,
        'key_required' => true,
        'limit' => '100 requêtes/jour',
        'description' => 'Collections d\'art européennes',
        'category' => 'Œuvres d\'art françaises'
    ],
    'paris_musees' => [
        'url' => PARIS_MUSEES_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Collections des 14 musées parisiens',
        'category' => 'Œuvres d\'art françaises'
    ],
    'images_art' => [
        'url' => IMAGES_ART_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Images haute résolution RMN-GP',
        'category' => 'Œuvres d\'art françaises'
    ],
    
    // Œuvres d'art internationales
    'met_museum' => [
        'url' => MET_MUSEUM_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Collections du Metropolitan Museum',
        'category' => 'Œuvres d\'art internationales'
    ],
    'harvard_art' => [
        'url' => HARVARD_ART_API_URL,
        'key_required' => true,
        'limit' => '100 000 requêtes/jour',
        'description' => 'Collections Harvard Art Museums',
        'category' => 'Œuvres d\'art internationales'
    ],
    'chicago_art' => [
        'url' => CHICAGO_ART_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Collections Art Institute of Chicago',
        'category' => 'Œuvres d\'art internationales'
    ],
    
    // Données structurées
    'wikidata' => [
        'url' => WIKIDATA_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Base de connaissances structurée',
        'category' => 'Données structurées'
    ],
    'dbpedia' => [
        'url' => DBPEDIA_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Données structurées Wikipedia',
        'category' => 'Données structurées'
    ],
    
    // Localisation
    'nominatim' => [
        'url' => NOMINATIM_API_URL,
        'key_required' => false,
        'limit' => '1 requête/seconde',
        'description' => 'Localisation OpenStreetMap',
        'category' => 'Localisation'
    ],
    'opencage' => [
        'url' => OPENCAGE_API_URL,
        'key_required' => true,
        'limit' => '2500 requêtes/jour',
        'description' => 'Géocodage avancé',
        'category' => 'Localisation'
    ],
    
    // Météo
    'openweather' => [
        'url' => OPENWEATHER_API_URL,
        'key_required' => true,
        'limit' => '1000 requêtes/jour',
        'description' => 'Météo pour les visites',
        'category' => 'Météo'
    ],
    
    // Musées internationaux
    'rijksmuseum' => [
        'url' => RIJKSMUSEUM_API_URL,
        'key_required' => false,
        'limit' => '5000 requêtes/jour',
        'description' => 'Rijksmuseum Amsterdam (1M+ œuvres)',
        'category' => 'Musées internationaux'
    ],
    'british_museum' => [
        'url' => BRITISH_MUSEUM_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'British Museum Londres (4M+ objets)',
        'category' => 'Musées internationaux'
    ],
    'wikimedia_commons' => [
        'url' => WIKIMEDIA_COMMONS_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Images libres Wikimedia',
        'category' => 'Musées internationaux'
    ],
    'pop' => [
        'url' => POP_API_URL,
        'key_required' => false,
        'limit' => 'Illimité',
        'description' => 'Patrimoine français (4M+ objets)',
        'category' => 'Musées internationaux'
    ],
    'smithsonian' => [
        'url' => SMITHSONIAN_API_URL,
        'key_required' => true,
        'limit' => '1000 requêtes/jour',
        'description' => 'Collections Smithsonian (3M+ objets)',
        'category' => 'Musées internationaux'
    ]
]);
?>
