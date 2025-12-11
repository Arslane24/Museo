<?php
/**
 * API ENDPOINT - Œuvres d'art d'un musée
 * 
 * Paramètres GET :
 * - id : ID du musée
 * - source : Source de l'API (harvard, met, rijksmuseum)
 * - limit : Nombre max d'œuvres (défaut: 20, max: 50)
 * 
 * Retourne JSON avec la liste des œuvres
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/services/ExternalMuseumService.php';

// Récupération des paramètres
$museumId = $_GET['id'] ?? null;
$apiSource = $_GET['source'] ?? 'met';
$limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));

// Validation
if (!$museumId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Museum ID is required'
    ]);
    exit;
}

try {
    $service = new ExternalMuseumService($pdo);
    $artworks = $service->getMuseumArtworks($museumId, $apiSource, $limit);
    
    // Log pour debug
    error_log("Museum Artworks API called - ID: $museumId, Source: $apiSource, Results: " . count($artworks));
    
    echo json_encode([
        'success' => true,
        'artworks' => $artworks,
        'count' => count($artworks),
        'source' => $apiSource,
        'museum_id' => $museumId,
        'debug' => [
            'request_params' => $_GET,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Museum Artworks API Error: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
