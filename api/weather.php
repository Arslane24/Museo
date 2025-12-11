<?php
/**
 * API ENDPOINT - Météo d'une ville
 * 
 * Paramètres GET :
 * - city : Nom de la ville
 * 
 * Retourne JSON avec données météo
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/services/ExternalMuseumService.php';

// Récupération de la ville
$city = $_GET['city'] ?? null;

// Validation
if (!$city) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'City name is required'
    ]);
    exit;
}

try {
    $service = new ExternalMuseumService($pdo);
    $weather = $service->getWeather($city);
    
    if ($weather) {
        // Formater les données météo
        $formatted = [
            'temperature' => $weather['main']['temp'] ?? null,
            'feels_like' => $weather['main']['feels_like'] ?? null,
            'description' => $weather['weather'][0]['description'] ?? '',
            'icon' => $weather['weather'][0]['icon'] ?? '',
            'humidity' => $weather['main']['humidity'] ?? null,
            'wind_speed' => $weather['wind']['speed'] ?? null,
            'city' => $weather['name'] ?? $city
        ];
        
        echo json_encode([
            'success' => true,
            'weather' => $formatted,
            'raw' => $weather
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Weather data not found',
            'city' => $city
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
