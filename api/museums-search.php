<?php
/**
 * API ENDPOINT - Recherche de musées AJAX
 */

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0); // Ne pas afficher les erreurs en HTML
header('Content-Type: application/json');

// Chargement de la base de données
$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);

// Récupération des paramètres
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$country = $_GET['country'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? min(1000, max(1, (int)$_GET['limit'])) : 12;
$offset = ($page - 1) * $limit;

try {
    // Recherche des musées
    $museums = $museumManager->searchMuseums($search, $category, $country, $limit, $offset);
    $total = $museumManager->countMuseums($search, $category, $country);
    
    // Vérifier si l'utilisateur est connecté pour les favoris
    $userId = $_SESSION['user_id'] ?? null;
    
    // Ajouter l'info favoris pour chaque musée
    if ($userId) {
        foreach ($museums as &$museum) {
            $museum['is_favorite'] = $museumManager->isFavorite($userId, $museum['id']);
        }
    }
    
    echo json_encode([
        'success' => true,
        'museums' => $museums,
        'pagination' => [
            'current_page' => $page,
            'total_results' => $total,
            'total_pages' => ceil($total / $limit),
            'per_page' => $limit
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la recherche: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
