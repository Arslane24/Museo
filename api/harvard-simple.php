<?php
/**
 * API Harvard Art Museums - Appel direct simplifié
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Récupération des paramètres
$objectId = $_GET['id'] ?? null;

// Validation
if (!$objectId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Object ID is required'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Clé API Harvard
$apiKey = '6ec8530d-1428-4f48-91b9-4d27b4eaf4d1';

// Construction de l'URL
$url = "https://api.harvardartmuseums.org/object/{$objectId}?apikey={$apiKey}";

// Appel à l'API avec curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Traitement de la réponse
if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    
    // Formater les données
    echo json_encode([
        'success' => true,
        'object' => [
            'id' => $data['id'] ?? null,
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'culture' => $data['culture'] ?? '',
            'period' => $data['period'] ?? '',
            'classification' => $data['classification'] ?? '',
            'medium' => $data['medium'] ?? '',
            'dimensions' => $data['dimensions'] ?? '',
            'dated' => $data['dated'] ?? '',
            'people' => array_map(function($person) {
                return [
                    'name' => $person['name'] ?? '',
                    'role' => $person['role'] ?? ''
                ];
            }, $data['people'] ?? []),
            'images' => array_map(function($image) {
                return [
                    'url' => $image['baseimageurl'] ?? '',
                    'alt' => $image['alttext'] ?? ''
                ];
            }, $data['images'] ?? []),
            'primary_image' => $data['primaryimageurl'] ?? '',
            'url' => $data['url'] ?? ''
        ],
        'raw' => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    // Erreur
    $errorData = json_decode($response, true);
    
    http_response_code($httpCode);
    echo json_encode([
        'success' => false,
        'error' => $errorData['error'] ?? 'Impossible de récupérer les données',
        'object_id' => $objectId,
        'http_code' => $httpCode,
        'api_response' => $errorData
    ], JSON_PRETTY_PRINT);
}
