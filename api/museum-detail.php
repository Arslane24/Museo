<?php
/**
 * API ENDPOINT - Détails d'un musée depuis une API externe
 * 
 * Paramètres GET :
 * - id : ID du musée dans notre BDD
 * - source : Source de l'API (harvard, met, rijksmuseum)
 * 
 * Retourne JSON avec les détails du musée
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Chargement de la base de données
$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/services/ExternalMuseumService.php';
require_once __DIR__ . '/../src/models/MuseumManager.php';

// Récupération des paramètres
$museumId = $_GET['id'] ?? null;
$apiSource = $_GET['source'] ?? null;

// Validation
if (!$museumId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Museum ID is required'
    ]);
    exit;
}

// Si pas de source spécifiée, retourner le musée de notre BDD
if (!$apiSource) {
    try {
        $museumManager = new MuseumManager($pdo);
        $museum = $museumManager->getMuseumById((int)$museumId);
        
        if (!$museum) {
            echo json_encode([
                'success' => false,
                'error' => 'Musée introuvable'
            ]);
            exit;
        }

        // Vérifier si l'utilisateur est connecté pour les favoris
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $museum['is_favorite'] = $museumManager->isFavorite($userId, $museum['id']);
        } else {
            $museum['is_favorite'] = false;
        }

        echo json_encode([
            'success' => true,
            'museum' => $museum
        ]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur serveur',
            'details' => $e->getMessage()
        ]);
        exit;
    }
}

try {
    $service = new ExternalMuseumService($pdo);
    $details = $service->getMuseumDetails($museumId, $apiSource);
    
    if ($details) {
        echo json_encode([
            'success' => true,
            'data' => $details,
            'source' => $apiSource,
            'cached' => false // TODO: détecter si vient du cache
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No data found for this museum',
            'museum_id' => $museumId,
            'source' => $apiSource
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
