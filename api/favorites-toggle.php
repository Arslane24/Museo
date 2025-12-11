<?php
// Toujours au tout début
session_start();

// Headers AVANT toute sortie
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'not_logged'
    ]);
    exit;
}

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/models/MuseumManager.php';

// Lire JSON
$input = json_decode(file_get_contents('php://input'), true);
$museumId = $input['museum_id'] ?? null;

if (!$museumId) {
    echo json_encode([
        'success' => false,
        'error' => 'Museum ID is required'
    ]);
    exit;
}

try {
    $museumManager = new MuseumManager($pdo);
    $userId = $_SESSION['user_id'];

    $isFavorite = $museumManager->isFavorite($userId, $museumId);

    if ($isFavorite) {
        $museumManager->removeFromFavorites($userId, $museumId);
        $newStatus = false;
    } else {
        $museumManager->addToFavorites($userId, $museumId);
        $newStatus = true;
    }

    echo json_encode([
        'success' => true,
        'is_favorite' => $newStatus,
        'museum_id' => $museumId
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'server_error',
        'message' => $e->getMessage()
    ]);
}
