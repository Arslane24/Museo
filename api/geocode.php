<?php
/**
 * API ENDPOINT - Géocodage d'une adresse
 * 
 * Paramètres GET :
 * - address : Adresse complète à géocoder
 * 
 * Retourne JSON avec lat/lng
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/services/ExternalMuseumService.php';

// Récupération de l'adresse
$address = $_GET['address'] ?? null;

// Validation
if (!$address) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Address is required'
    ]);
    exit;
}

try {
    $service = new ExternalMuseumService($pdo);
    $coords = $service->geocodeAddress($address);
    
    if ($coords) {
        echo json_encode([
            'success' => true,
            'coordinates' => $coords,
            'address' => $address
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Address not found',
            'address' => $address
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
