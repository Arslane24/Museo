<?php
/**
 * API ENDPOINT - Toggle favori (ajouter/retirer)
 * 
 * Méthode : POST
 * Body JSON :
 * {
 *   "museum_id": 1
 * }
 * 
 * Retourne JSON avec le nouveau statut
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'You must be logged in'
    ]);
    exit;
}

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/models/MuseumManager.php';

// Récupérer le JSON
$input = json_decode(file_get_contents('php://input'), true);
$museumId = $input['museum_id'] ?? null;

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
    $museumManager = new MuseumManager($pdo);
    $userId = $_SESSION['user_id'];
    
    // Vérifier si déjà favori
    $isFavorite = $museumManager->isFavorite($userId, $museumId);
    
    if ($isFavorite) {
        // Retirer des favoris
        $museumManager->removeFromFavorites($userId, $museumId);
        $newStatus = false;
        $message = 'Removed from favorites';
    } else {
        // Ajouter aux favoris
        $museumManager->addToFavorites($userId, $museumId);
        $newStatus = true;
        $message = 'Added to favorites';
    }
    
    echo json_encode([
        'success' => true,
        'is_favorite' => $newStatus,
        'message' => $message,
        'museum_id' => $museumId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
